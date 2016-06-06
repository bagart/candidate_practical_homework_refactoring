<?php
namespace Language;
use Language\Iface;
use Language\Module;
use Language\Module\Cache;
use Language\Module\Traits;

/**
 * Business logic related to generating language files.
 */
final class LanguageBatchBo implements Iface\LanguageGenerate
{
	use Traits\Logger;
	
	private $applications = null;
	
	private $applets = [
		'memberapplet' => 'JSM2_MemberApplet',
	];

	/**
	 * @var Iface\LanguageApiCall|null
	 */
	private $language_api_call = null;
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
		$this->getLogger()->info("Generating language php files: START");

		foreach ($this->getApplications() as $application => $languages) {
			$this->getLogger()->info("generate: $application with languages: " . implode(', ', $languages));
			foreach ($languages as $language) {
				$this->getLogger()->debug("\tLANGUAGE $language: try");
				try {
					$this->generateLanguagePhpFile($language, $application);
					$this->getLogger()->info("\tLANGUAGE $language: OK");
				} catch (Iface\LanguageException $e) {
					$this->getLogger()->error("\tLANGUAGE $language: ERROR");
				}
			}
		}
		$this->getLogger()->info("Generating language php files: END");
		
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
	 * @return Cache\Php   The success of the operation.
	 */
	protected function generateLanguagePhpFile($language, $application)
	{
		return (new Cache\Php($language, $application))
			->store(
				$this->getLanguageApiCall()
					->getLanguageFile($language)
			);

	}

	/**
	 * @return Iface\LanguageApiCall
	 */
	protected function getLanguageApiCall()
	{
		if (!$this->language_api_call) {
			$this->language_api_call = new Module\ApiCall\Language();
		}
		return $this->language_api_call;
	}

	/**
	 * @param $language
	 * @param $appletLanguageId
	 * @return $this
	 * @throws Iface\LanguageException
	 */
	protected function generateLanguageXMLFile($language, $appletLanguageId)
	{
		return (new Cache\Xml($language))
			->store(
				$this->getLanguageApiCall()
					->getAppletLanguageFile($language, $appletLanguageId)
			);
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
		$this->getLogger()->info("Generate applet language XMLs: START");

		foreach ($this->getApplets() as $appletDirectory => $appletLanguageId) {

			$this->getLogger()
				->info("\tgenerate: $appletLanguageId ($appletDirectory) language xmls: START");

			$languages = $this->getLanguageApiCall()
				->setAllowEmptyData(false)
				->getAppletLanguages($appletLanguageId);

			$this->getLogger()
				->info("\tAvailable languages: " . implode(', ', $languages));

			foreach ($languages as $language) {
				$this->getLogger()->debug("\t\tgenerate XML $language ($appletLanguageId): try");
				try {
					$this->generateLanguageXMLFile($language, $appletLanguageId);
					$this->getLogger()->info("\t\tgenerate XML $language ($appletLanguageId): OK");
				} catch (Iface\LanguageException $e) {
					$this->getLogger()->error("\t\tgenerate XML $language ($appletLanguageId): ERROR");
				}
				
			}
			$this->getLogger()
				->info("\tgenerate: $appletLanguageId ($appletDirectory) language xmls: END");
		}
		$this->getLogger()->info("Generate applet language XMLs: END");

		return $this;
	}

	protected function getApplets()
	{
		return (array) $this->applets;
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
}
