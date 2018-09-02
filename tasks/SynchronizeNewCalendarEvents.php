<?php
/**
 * @brief		SynchronizeNewCalendarEvents Task
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 * @package		Invision Community
 * @subpackage	discord
 * @since		02 Sep 2018
 */

namespace IPS\discord\tasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * SynchronizeNewCalendarEvents Task
 */
class _SynchronizeNewCalendarEvents extends \IPS\Task
{
	/**
	 * Execute
	 *
	 * If ran successfully, should return anything worth logging. Only log something
	 * worth mentioning (don't log "task ran successfully"). Return NULL (actual NULL, not '' or 0) to not log (which will be most cases).
	 * If an error occurs which means the task could not finish running, throw an \IPS\Task\Exception - do not log an error as a normal log.
	 * Tasks should execute within the time of a normal HTTP request.
	 *
	 * @return	mixed	Message to log or NULL
	 * @throws	\IPS\Task\Exception
	 */
	public function execute()
	{
        /** @var \IPS\discord\Discord\CalendarEventsToSync[] $eventsToSync */
        $eventsToSync = new \IPS\Patterns\ActiveRecordIterator(
            \IPS\Db::i()->select( '*', 'discord_sync_calendar_events' ),
            \IPS\discord\Discord\CalendarEventsToSync::class
        );

        $idsToRemove = [];

        foreach ($eventsToSync as $eventToSync)
        {
            $eventId = $eventToSync->calendar_event_id;
            $event = \IPS\calendar\Event::load($eventId);

            try
            {
                \IPS\discord\Api\Channel::i()->createMessage(
                    $eventToSync->discord_channel_id,
                    [
                        'content' => $event->author()->name . ' just created a new event: ' . $event->title
                    ]
                );

                $idsToRemove[] = $eventToSync->id;
            }
            catch (\Exception $e) {}
        }

        \IPS\Db::i()->delete('discord_sync_calendar_events', \IPS\Db::i()->in('id', $idsToRemove));

        return NULL;
	}
}