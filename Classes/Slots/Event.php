<?php
/**
 * Event search service
 *
 * @package Calendarize\Slots
 * @author  Tim Lochmüller
 */

namespace HDNET\Calendarize\Slots;

use HDNET\Calendarize\Domain\Repository\EventRepository;
use HDNET\Calendarize\Utility\HelperUtility;

/**
 * Event search service
 *
 * @author Tim Lochmüller
 */
class Event {

	/**
	 * Check if we can reduce the amount of results
	 *
	 * @signalClass \HDNET\Calendarize\Domain\Repository\IndexRepository
	 * @signalName findBySearchPre
	 *
	 * @param array          $indexIds
	 * @param \DateTime|NULL $startDate
	 * @param \DateTime|NULL $endDate
	 * @param array          $customSearch
	 *
	 * @return array|void
	 */
	public function setIdsByCustomSearch(array $indexIds, \DateTime $startDate = NULL, \DateTime $endDate = NULL, array $customSearch) {
		if (!isset($customSearch['fullText'])) {
			return NULL;
		}
		/** @var EventRepository $eventRepository */
		$eventRepository = HelperUtility::create('HDNET\\Calendarize\\Domain\\Repository\\EventRepository');
		return array(
			'indexIds'     => $eventRepository->getIdsBySearchTerm($customSearch['fullText']),
			'startDate'    => $startDate,
			'endDate'      => $endDate,
			'customSearch' => $customSearch,
		);
	}

	/**
	 * Set ids by general
	 *
	 * @signalClass \HDNET\Calendarize\Domain\Repository\IndexRepository
	 * @signalName getDefaultConstraints
	 *
	 * @param array $indexIds
	 * @param array $indexTypes
	 * @param array $contentRecord
	 *
	 * @return array
	 */
	public function setIdsByGeneral(array $indexIds, array $indexTypes, array $contentRecord) {
		$databaseConnection = HelperUtility::getDatabaseConnection();
		$rows = $databaseConnection->exec_SELECTgetRows('uid_local', 'sys_category_record_mm', 'tablenames="tt_content" AND uid_foreign=' . $contentRecord['uid']);
		$categoryIds = array();
		foreach ($rows as $row) {
			$categoryIds[] = (int)$row['uid_local'];
		}

		if (empty($categoryIds)) {
			return array(
				'indexIds'      => $indexIds,
				'indexTypes'    => $indexTypes,
				'contentRecord' => $contentRecord,
			);
		}

		$rows = $databaseConnection->exec_SELECTgetRows('uid_foreign', 'sys_category_record_mm', 'tablenames="tx_calendarize_domain_model_event" AND uid_local IN (' . implode(',', $categoryIds) . ')');
		foreach ($rows as $row) {
			$indexIds[] = (int)$row['uid_foreign'];
		}

		return array(
			'indexIds'      => $indexIds,
			'indexTypes'    => $indexTypes,
			'contentRecord' => $contentRecord,
		);
	}
} 