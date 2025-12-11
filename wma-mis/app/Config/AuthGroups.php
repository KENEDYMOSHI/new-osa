<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'officer';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the System.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the System.',
        ],
        'headofsection' => [
            'title'       => 'Head Of Section',
            'description' => 'Top management',
        ],
        'surveillance' => [
            'title'       => 'Surveillance Team',
            'description' => 'Has access to Surveillance related features.',
        ],
        'manager' => [
            'title'       => 'Regional Manager',
            'description' => 'Has access to manager related features.',
        ],
        'officer' => [
            'title'       => 'Inspection Officer',
            'description' => 'Has access to officer related features.',
        ],
        'accountant' => [
            'title'       => 'Regional Accountant',
            'description' => 'Has access to accountant related features.',
        ],
        'accountant-hq' => [
            'title'       => 'Head Quarters Accountant',
            'description' => 'Has access to accountant related features.',
        ],
        'audit' => [
            'title'       => 'Internal Audit',
            'description' => 'Has access to certain activities in system  .',
        ],
        'dts' => [
            'title'       => 'DTS',
            'description' => 'Dts  .',
        ],
        'ceo' => [
            'title'       => 'CEO',
            'description' => 'Ceo .',
        ],
    ];


    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'admin.access'        => 'Can access the sites admin area',
        'admin.settings'      => 'Can access the main site settings',
        'users.manage-admins' => 'Can manage other admins',
        'users.create'        => 'Can create new non-admin users',
        'users.edit'          => 'Can edit existing non-admin users',
        'users.ban'          => 'Can ban/Unban non-admin users',
        'users.delete'        => 'Can delete existing non-admin users',
        'bill.create'         => 'Can Create bill',
        'bill.access'         => 'Can Access bill features',
        'bill.cancel'         => 'Can cancel bill',
        'bill.renew'         => 'Can renew bill',
        'bill.cancelapproval' => 'Can cancel bill',
        'activities.create'   => 'Can Add instrument',
        'activities.view'     => 'Can view registered instruments',
        'activities.search'   => 'Can search instruments',
        'application.access'    => 'Can access all features related to license and service application',
        'report.sticker' => 'Can view sticker report',
        'report.gfs' => 'Can view gfscode report',
        'report.sticker' => 'Can view sticker report',
        'report.target' => 'Can view target/performance report',
        'report.collectionSummary' => 'Can view collection summary report',
        'projection.edit' => 'Can edit Projection ',
        'projection.delete' => 'Can delete Projection',
        'projection.access' => 'Can delete Projection',
        'reconciliation.access' => 'Can Access  reconciliation',
        'admin-recon.access' => 'Can Access  reconciliation(Admin)',
        'receivables.access' => 'Can Access  Receivables',
        'report.variance-analysis' => 'Can view variance report',
        'estimates.manage' => 'Can manage collection estimates',
        'activity-estimates.manage' => 'Can manage activity estimates',

      


    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
           'users.*',
            'bill.*',
            'activities.*',
            'application.access',
            'report.*',
            'projection.access',
            'recon.access',
            'app.*',
            'estimates.manage'
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'activities.search',
            'projection.access',
           // 'reconciliation.access',
            'report.*',
        ],
        'headofsection' => [
            'bill.*',
            'activities.view',
            'activities.search',
            'estimates.manage'
        ],
        'surveillance' => [
            'bill.cerate',
            'activities.view',
            'activities.search',
            'projection.access'
        ],
        'officer' => [
            'activities.*',
            'bill.access',
            'bill.create',
            


        ],

        'manager' => [
            'bill.*',
            'activities.view',
            'activities.create',
            'activities.search',
            'application.access',
            'projection.access',
            'activity-estimates.manage'

        ],
        'accountant' => [
            'bill.access',
            'activities.search',
            'projection.access',
            'bill.create',
            'activities.create',
            'receivables.access',
            'estimates.manage'



        ],
        'accountant-hq' => [
            'bill.access',
            'activities.search',
            'projection.access',
            'bill.create',
            'activities.create',
            'receivables.access',
            'estimates.manage'



        ],
        'audit' => [
            'report.*',
            'activities.search',
            'projection.access',
            'estimates.manage'


        ],

    ];
}
