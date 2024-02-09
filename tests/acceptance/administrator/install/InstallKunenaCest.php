<?php

/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
class InstallKunenaCest
{
    /**
     * Install Kunena
     *
     * @param   AcceptanceTester  $I
     *
     * @return void
     */
    public function installKunena(AcceptanceTester $I)
    {
        $I->doAdministratorLogin();
        $I->amOnPage('/administrator/index.php?option=com_installer');
        $I->waitForText('Extensions: Install', '30', ['css' => 'H1']);
        $I->click(['link' => 'Install from Folder']);
        $url = $I->getConfiguration('repo_folder');
        $I->fillField(['id' => 'install_directory'], $url);
        $I->click(['id' => 'installbutton_directory']); // Install button// Install button
        $I->wait(5);
        $I->comment('Close the installer');
        $I->amOnPage('administrator/index.php?option=com_kunena');
        $I->wait(1);
        $I->doAdministratorLogout();
    }
}
