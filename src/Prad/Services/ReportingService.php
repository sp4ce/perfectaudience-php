<?php
namespace Prad\Services;

use Prad\Util\Config;

/**
 * Performs all actions pertaining to reporting the Perfect Audience.
 *
 * @package Services
 * @author  Baptiste Pernet
 */
class ReportingService extends BaseService {

	/**
	 * Get the status of the reporting.
	 * Use the data status service to check when new performance data is available.
	 * @params string $dataType - type of report we get the date: campaign_report, ad_report, conversion_report
	 * @return string - The UTC timestamp at which the data source was last updated.
	 */
	public function getStatus($dataType) {
		$baseUrl = Config::get('endpoints.base_url') . Config::get('endpoints.report.status');
		$url = $this->buildUrl($baseUrl, array('data_type' => $dataType));

		$response = $this->getRestClient()->get($url, $this->getHeaders());
		$jsonResponse = json_decode($response->body);

		return $jsonResponse->data_status->last_updated;
	}

	/**
	 * It returns useful performance statistics over a given time interval, separated by campaign.
	 * @params array $params - parameters of the query.
	 * @return the response object.
	 */
	public function getCampaignReport(array $params = null) {
		$baseUrl = Config::get('endpoints.base_url') . Config::get('endpoints.report.campaign');
		$url = $this->buildUrl($baseUrl, $params);

		$response = $this->getRestClient()->get($url, $this->getHeaders());
		$jsonResponse = json_decode($response->body);

		return $jsonResponse->report;
	}

	/**
	 * Returns metadata for a campaign within an account
	 * @param string $id - campaign id.
	 */
	public function getCampaign($id) {
		// Build the url.
		$url = Config::get('endpoints.base_url') . Config::get('endpoints.campaign');
		$url .= "/$id";

		// Make the request and json decode.
		$response = $this->getRestClient()->get($url, $this->getHeaders());
		$jsonResponse = json_decode($response->body);

		// Return the object.
		return $jsonResponse->campaign;
	}

	/**
	 * Returns metadata for campaigns within an account
	 * @param string $siteId - Restricts the results to campaigns underneath the site with the given ID.
	 * Site IDs may be retrieved from the /sites endpoint.
	 */
	public function getCampaigns($siteId = null) {
		// Build the url.
		$url = Config::get('endpoints.base_url') . Config::get('endpoints.campaign');
		if (!is_null($siteId)) {
			$url = $this->buildUrl($url, array('site_id' => $siteId));
		}

		// Make the request.
		$response = $this->getRestClient()->get($url, $this->getHeaders());
		$jsonResponse = json_decode($response->body);

		// Return the list of campaigns.
		return $jsonResponse->campaigns;
	}
}
