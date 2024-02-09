<?php

/**
 * Kunena Component
 * @package       Kunena.UnitTest
 * @subpackage    Utilities
 *
 * @Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

defined('_JEXEC') or die();

/**
 * Test class for KunenaForumTopicUserHelper.
 */
class KunenaForumTopicUserHelperCest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get()
     */
    public function testGet()
    {
        $admin = KunenaFactory::getUser('admin');

        $topicuser = KunenaForumTopicUserHelper::get();
        $this->assertEquals(null, $topicuser->topic_id);
        $this->assertEquals(0, $topicuser->user_id);
    }

    /**
     * Test getTopics()
     */
    public function testGetTopics()
    {
        list($count, $topics) = KunenaForumTopicHelper::getLatestTopics(false, 0, 20);
        $topicusers = KunenaForumTopicUserHelper::getTopics($topics);
        foreach ($topics as $topic) {
            $this->assertTrue(isset($topicusers[$topic->id]));
            $this->assertEquals($topic->id, $topicusers[$topic->id]->topic_id);
        }
    }
}
