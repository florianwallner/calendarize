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

		// @todo reduce by category
		// DebuggerUtility::var_dump($contentRecord);

		return array(
			'indexIds'      => $indexIds,
			'indexTypes'    => $indexTypes,
			'contentRecord' => $contentRecord,
		);
	}
} 