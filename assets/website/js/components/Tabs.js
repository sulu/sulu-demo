import $ from 'jquery';

export default class Tabs {
    initialize(el, options) {
        this.$el = $(el);
        this.$tabs = this.$el.children();
        this.activeTabClass = this.$tabs.first().attr('class').split(' ')[0] + '--active';
        this.$container = options.container ? $('#' + options.container) : $('#' + this.$el.attr('id') + '-container');
        this.$containers = this.$container.children();
        this.hash = false;
        this.didSetInitialTab = false;

        if (options.hash) {
            this.hash = true;
        }

        this.$tabs.each(function(index, el) {
            var $tab = $(el);
            $tab.click(function() {
                this.changeTab($tab, index);
            }.bind(this));

            if (this.hash && window.location.hash && window.location.hash === '#' + $tab.data('name')) {
                $tab.click();
                this.didSetInitialTab = true;
            }
        }.bind(this));

        if (!this.didSetInitialTab) {
            this.$tabs.first().click();
        }
    }

    changeTab($el, index) {
        var hashName = $el.data('name');
        this.$tabs.attr('disabled', false);
        $el.attr('disabled', true);
        this.$tabs.removeClass(this.activeTabClass);
        $el.addClass(this.activeTabClass);
        this.$containers.hide();
        this.$containers.eq(index).show();

        if (this.hash) {
            window.location.hash = hashName ? hashName : '';
        }
    }
}
