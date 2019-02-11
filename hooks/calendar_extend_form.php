//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class discord_hook_calendar_extend_form extends _HOOK_CLASS_
{
    /**
     * [Node] Add/Edit Form
     *
     * @param   \IPS\Helpers\Form   $form   The form
     * @return  void
     */
    public function form( &$form )
    {
        parent::form( $form );

        $channels = \IPS\discord\Api\Guild\Channel::i()->all(\IPS\Settings::i()->discord_guild_id);
        $channels = \IPS\discord\Util\ChannelFormatter::onlyTextChannels($channels);
        $channels = \IPS\discord\Util\ChannelFormatter::onlyIdsAndNames($channels);
        $channels = \IPS\discord\Util\ChannelFormatter::addEmptyChannel($channels);

        $form->addHeader( 'discord_channels' );
        $form->add(
            new \IPS\Helpers\Form\Select( 'cal_discord_channel_approved', $this->discord_channel_approved ?: 0, TRUE, [
                'options' => $channels
            ] )
        );
        $form->add(
            new \IPS\Helpers\Form\Select( 'cal_discord_channel_unapproved', $this->discord_channel_unapproved ?: 0, TRUE, [
                'options' => $channels
            ] )
        );

        $form->addHeader( 'discord_notifications' );
        $form->add(
            new \IPS\Helpers\Form\TextArea(
                'cal_discord_post_format',
                $this->discord_post_format ?: '{poster} has just created a new event called: "{title}". Read more: {link}',
                TRUE
            )
        );
    }
}