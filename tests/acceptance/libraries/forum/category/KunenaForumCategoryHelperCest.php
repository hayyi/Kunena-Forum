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
 * Test class for KunenaForumCategoryHelper.
 */
class KunenaForumCategoryHelperCest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getCategories()
     */
    public function testGetAllCategories()
    {
        $categories = KunenaForumCategoryHelper::getCategories();
        $this->assertInternalType('array', $categories);
        foreach ($categories as $id => $category) {
            $this->assertEquals($id, $category->id);
            $this->assertTrue($category->exists());
            $this->assertSame($category, KunenaForumCategoryHelper::get($id));
        }
    }
}
