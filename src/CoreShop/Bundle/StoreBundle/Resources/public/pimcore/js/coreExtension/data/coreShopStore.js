/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopStore');
pimcore.object.classes.data.coreShopStore = Class.create(coreshop.object.classes.data.select, {

    type: 'coreShopStore',

    getTypeName: function () {
        return t('coreshop_store');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_store';
    }
});
