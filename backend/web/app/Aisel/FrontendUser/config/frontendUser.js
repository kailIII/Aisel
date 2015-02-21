'use strict';

/**
 * This file is part of the Aisel package.
 *
 * (c) Ivan Proskuryakov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @name            AiselFrontendUser
 * @description     Module configuration
 */

define(['app'], function (app) {
    app.config(['$stateProvider', function ($stateProvider) {
        $stateProvider
            .state("frontendUsers", {
                url: "/:locale/users/frontend/",
                templateUrl: '/app/Kernel/Resource/views/collection.html',
                controller: 'FrontendUserCtrl'
            })
            .state("frontendUserView", {
                url: "/:locale/users/frontend/view/:id/",
                templateUrl: '/app/Aisel/FrontendUser/views/detail.html',
                controller: 'FrontendUserDetailCtrl'
            })
    }]);
});