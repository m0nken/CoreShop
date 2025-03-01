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

pimcore.registerNS('coreshop.order.order.create.step');
pimcore.registerNS('coreshop.order.order.create.abstractStep');
coreshop.order.order.create.abstractStep = Class.create({
    eventManager: null,
    creationPanel: null,

    initialize: function (creationPanel, eventManager) {
        var me = this;

        me.creationPanel = creationPanel;
        me.eventManager = eventManager;

        if (Ext.isFunction(me.initStep)) {
            me.initStep();
        }
    },

    isValid: function () {
        return true;
    },

    reset: function() {

    },

    getPriority: function () {
        Ext.Error.raise('implement me');
    },

    getValues: function () {
        Ext.Error.raise('implement me');
    },

    getPreviewValues: function () {
        return this.getValues();
    },

    getName: function() {
        Ext.Error.raise('implement me');
    },

    getPanel: function() {
        Ext.Error.raise('implement me');
    },

    setPreviewData: function(data) {
        Ext.Error.raise('implement me');
    },

    getLayout: function () {
        var tools = Ext.isFunction(this.getTools) ? this.getTools() : [];
        var iconCls = Ext.isFunction(this.getIconCls) ? this.getIconCls() : '';
        var panel = this.getPanel();

        this.panel = panel;
        this.layout = new Ext.panel.Panel({
            margin: '15 0 15 0',
            iconCls: iconCls,
            title: this.getName(),
            items: panel,
            tools: tools
        });

        return this.layout;
    }
});
