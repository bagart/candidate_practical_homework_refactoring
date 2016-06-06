<?php
namespace Language;

use Language\Exception as E;
use Language\Module\Traits;

/**
 * Business logic related to generating language files.
 */
final class LanguageBatchBo implements Iface\LanguageGenerate
{
	use Traits\Logger;
	
	private $applications = null;
	private $path_root = null;
	
	private $applets = [
		'memberapplet' => 'JSM2_MemberApplet',
	];

	/**
	 * Starts the language file generation.
	 *
	 * @throws Iface\LanguageException
	 * 
	 * @return $this
	 */
	public function generateLanguageFiles()
	{
		// The applications where we need to translate.
	
		$this->getLogger()->info("\nGenerating language files\n");

		foreach ($this->getApplications() as $application => $languages) {
			$this->getLogger()->info("[APPLICATION: $application]}\n");
			foreach ($languages as $language) {
				$this->getLogger()->debug("\t[LANGUAGE: $language] try\n");
				$this->getLanguageFile($application, $language);
				$this->getLogger()->info("\t[LANGUAGE: $language] OK\n");
			}
		}
		
		return $this;
	}
	
	/**
	 * Gets the language file for the given language and stores it.
	 *
	 * @param string $application   The name of the application.
	 * @param string $language      The identifier of the language.
	 *
	 * @throws Iface\LanguageException   If there was an error during the download of the language file.
	 *
	 * @return bool   The success of the operation.
	 */
	public function getLanguageFile($application, $language)
	{
		$language_data = (new Module\ApiCall\Language)->getLanguageFile($language);

		// If we got correct data we store it.
		$destination = $this->getLanguageCacheAppFileName($application, $language);

		if (!is_dir(dirname($destination))) {
			if (!mkdir(dirname($destination), 0755, true)) {
				throw new E\ErrorResult("error on mkdir {$destination} for LanguageFile");
			}
		}

		if (!file_put_contents($destination, $language_data)) {
			throw new E\ErrorResult("error on write LanguageFile: $destination");
		}

		return true;
	}

	/**
	 * Gets the file name of the cached application language php.
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function getLanguageCacheAppFileName($application, $language)
	{
		return  "{$this->getPathRoot()}/cache/$application/{$language}.php";
	}

	/**
	 * Gets the file name of the cached flash language xml.
	 * 
	 * @param string $language
	 * 
	 * @return string
	 */
	public function getLanguageCacheXmlFileName($language)
	{
		return  "{$this->getPathRoot()}/cache/flash/lang_{$language}.xml";
	}

	/**
	 * Gets the language files for the applet and puts them into the cache.
	 *
	 * @throws Iface\LanguageException   If there was an error.
	 *
	 * @return $this;
	 */
	public function generateAppletLanguageXmlFiles()
	{
		$this->getLogger()->info("\nGetting applet language XMLs..\n");

		foreach ($this->applets as $appletDirectory => $appletLanguageId) {
			$this->getLogger()->info(" Getting > $appletLanguageId ($appletDirectory) language xmls..\n");

			$languages = (new Module\ApiCall\Language)
				->setAllowEmptyData(false)
				->getAppletLanguages($appletLanguageId);

			$this->getLogger()->info(' - Available languages: ' . implode(', ', $languages) . "\n");

			foreach ($languages as $language) {
				$xmlContent = (new Module\ApiCall\Language)
					->getAppletLanguageFile($appletLanguageId, $language);

				$xmlFile = $this->getLanguageCacheXmlFileName($language);
				if (!file_put_contents($xmlFile,$xmlContent)) {
					throw new E\ErrorResult(
						"Unable to save applet: ($appletLanguageId) "
						. "language: ($language) xml ($xmlFile)!"
					);
				}

				$this->getLogger()->info(" OK saving $xmlFile was successful.\n");
			}
			$this->getLogger()->info(" < $appletLanguageId ($appletDirectory) language xml cached.\n");
		}

		$this->getLogger()->info("\nApplet language XMLs generated.\n");

		return $this;
	}
		
	public function setApplications($applications)
	{
		$this->applications = $applications;

		return $this;
	}

	protected function getApplications()
	{
		if ($this->applications === null) {
			$this->setApplications(Config::get('system.translated_applications'));
		}

		return $this->applications;
	}

	public function setPathRoot($path_root)
	{
		$this->path_root = $path_root;

		return $this;
	}

	protected function getPathRoot()
	{
		if ($this->path_root === null) {
			$this->setPathRoot(Config::get('system.paths.root'));
		}

		return $this->path_root;
	}
}
