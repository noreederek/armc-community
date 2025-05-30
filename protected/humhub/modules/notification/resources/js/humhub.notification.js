
humhub.module('notification', function (module, require, $) {
    var util = require('util');
    var object = util.object;
    var string = util.string;
    var Widget = require('ui.widget').Widget;
    var event = require('event');
    var client = require('client');
    var view = require('ui.view');
    var user = require('user');

    var notificationIds = [];
    var notificationGroups = [];

    module.initOnPjaxLoad = true;

    var NotificationDropDown = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(NotificationDropDown, Widget);


    var OverviewWidget = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(OverviewWidget, Widget);

    OverviewWidget.prototype.init = function () {
        var that = this;
        event.on('humhub:notification:filterApplied', function (evt, form) {
            evt.preventDefault();
            that.reload({data: $(form).serializeArray()});
        });
    };


    NotificationDropDown.prototype.init = function (update) {
        this.isOpen = false;
        this.lastEntryLoaded = false;
        this.lastEntryId = 0;
        this.originalTitle = document.title;
        this.initDropdown();
        this.handleResult(update);

        var that = this;
        event.on('humhub:modules:notification:live:NewNotification', function (evt, events, update) {
            var filteredEvents = that.filterEvents(events);
            var count = (that.$.data('notification-count')) ? parseInt(that.$.data('notification-count')) + filteredEvents.length : filteredEvents.length;
            that.updateCount(count);
        });

        // e.g. Mail module can fire this to `updateTitle`.
        event.on('humhub:modules:notification:UpdateTitleNotificationCount', function (evt, events, update) {
            var count = (that.$.data('notification-count')) ? parseInt(that.$.data('notification-count')) : 0;
            updateTitle(count);
        });
    };

    NotificationDropDown.prototype.filterEvents = function (events) {
        if (!events || !events.length) {
            return;
        }

        var result = [];
        events.forEach(function (event) {
            if (notificationIds.indexOf(event.data.notificationId) < 0) {
                var groupId = event.data.notificationGroup;

                // We filter out group ids which were already handled
                if (!groupId || !groupId.length || notificationGroups.indexOf(groupId) < 0) {
                    result.push(event);
                    notificationGroups.push(groupId);
                }
            }
        });

        return result;
    };

    NotificationDropDown.prototype.initDropdown = function () {
        this.$entryList = this.$.find('ul.media-list');
        this.$dropdown = this.$.find('#dropdown-notifications');

        var that = this;
        this.$entryList.scroll(function () {
            var containerHeight = that.$entryList.height();
            var scrollHeight = that.$entryList.prop("scrollHeight");
            var currentScrollPosition = that.$entryList.scrollTop();

            // load more activites if current scroll position is near scroll height
            if (currentScrollPosition >= (scrollHeight - containerHeight - 1)) {
                if (!that.lastEntryLoaded) {
                    that.loadEntries();
                }
            }
        });
    };

    NotificationDropDown.prototype.toggle = function () {
        // Always reset the loading settings so we reload the whole dropdown.
        this.lastEntryLoaded = false;
        this.lastEntryId = 0;

        // Since the handler will be called before the bootstrap trigger it's an open event if the dropdown is not visible yet
        this.isOpen = !this.$dropdown.is(':visible');
        if (this.isOpen) {
            this.$entryList.empty().hide();
            this.loadEntries();
        }
    };

    NotificationDropDown.prototype.loadEntries = function () {
        if (this.loading) {
            return;
        }

        var that = this;
        this.loader();
        client.get(module.config.loadEntriesUrl, {data: {from: this.lastEntryId}})
            .then($.proxy(this.handleResult, this))
            .catch(_errorHandler)
            .finally(function () {
                that.loader(false);
                that.loading = false;
            });
    };

    NotificationDropDown.prototype.handleResult = function (response) {
        if (!response.counter) {
            this.$entryList.append(string.template(module.templates.placeholder, {'text': module.text('placeholder')}));
        } else {
            this.lastEntryId = response.lastEntryId;
            this.$entryList.append(response.output);

            $('span.time').timeago();
        }

        this.parseNotifications();
        this.updateCount(parseInt(response.newNotifications));
        this.lastEntryLoaded = (response.counter < 6);
        this.$entryList.fadeIn('fast');
    };

    NotificationDropDown.prototype.parseNotifications = function () {
        this.$entryList.find('[data-notification-id]').each(function () {
            var $this = $(this);
            var id = $this.data('notificationId');

            if (id && notificationIds.indexOf(id) < 0) {
                notificationIds.push(id);
            }

            var groupId = $this.data('notificationGroup');

            if (notificationGroups.indexOf(groupId) < 0) {
                notificationGroups.push($this.data('notificationGroup'));
            }
        });
    };

    NotificationDropDown.prototype.updateCount = function ($count) {
        if (this.$.data('notification-count') === $count) {
            if (!$count) {
                $('#badge-notifications').hide();
            }
            return;
        }

        event.trigger('humhub:notification:updateCount', [$count]);

        if (!$count) {
            updateTitle(false);
            $('#badge-notifications').html('0');
            $('#badge-notifications, #mark-seen-link').hide();
            $('#icon-notifications .fa').removeClass("animated swing");
        } else {
            updateTitle($count);
            $('#badge-notifications').html($count);
            $('#badge-notifications, #mark-seen-link').fadeIn('fast');

            // Clone icon to retrigger animation
            var $icon = $('#icon-notifications .fa');
            var $clone = $icon.clone();
            $clone.addClass("animated swing");
            $icon.replaceWith($clone);
        }

        this.$.data('notification-count', $count);
    };

    var getNotificationCount = function () {
        var widget = NotificationDropDown.instance('#notification_widget');
        return widget.$.data('notification-count');
    };

    var _errorHandler = function (e) {
        module.log.error(e, true);
    };

    NotificationDropDown.prototype.loader = function (show) {
        if (show !== false) {
            this.$.find('#loader_notifications').show();
        } else {
            this.$.find('#loader_notifications').hide();
        }

    };

    NotificationDropDown.prototype.markAsSeen = function (evt) {
        var that = this;
        return client.post(evt).then(function (response) {
            $('#badge-notifications').hide();
            $('#mark-seen-link').hide();
            that.updateCount(0);
            notificationIds = [];
            notificationGroups = [];
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    /**
     * Global action handler (used in overview page).
     *
     * @param {type} evt
     * @returns {undefined}
     */
    var markAsSeen = function (evt) {
        var widget = NotificationDropDown.instance('#notification_widget');
        widget.markAsSeen(evt).then(function () {
            location.reload();
        });
    };

    var updateTitle = function ($count) {
        // Workaround to include Mail Notification into Title
        // Mail Module triggers also `humhub:modules:notification:UpdateTitleNotificationCount` on New Messages
        if (humhub.modules.mail && humhub.modules.mail.notification && humhub.modules.mail.notification.getNewMessageCount) {
            $count += humhub.modules.mail.notification.getNewMessageCount() * 1;
        }

        if ($count) {
            document.title = '(' + $count + ') ' + view.getState().title;
        } else if ($count === false || $count === 0) {
            document.title = view.getState().title;
        }
    };

    module.templates = {
        placeholder: '<li class="placeholder">{text}</li>'
    };

    var init = function ($pjax) {
        if (user.isGuest()) {
            return;
        }

        if (!$('#notification_widget').length) {
            return;
        }

        updateTitle($('#notification_widget').data('notification-count'));
        initOverviewPage();

        if (!$pjax && view.isLarge()) {
            $("#dropdown-notifications ul.media-list").niceScroll({
                cursorwidth: "7",
                cursorborder: "",
                cursorcolor: "#555",
                cursoropacitymax: "0.2",
                nativeparentscrolling: false,
                railpadding: {top: 0, right: 3, left: 0, bottom: 0}
            });

            $("#dropdown-notifications ul.media-list").on('touchmove', function (evt) {
                evt.preventDefault();
            });
        }

        module.menu = NotificationDropDown.instance('#notification_widget');
    };

    var handleFilterChanges = function () {
        const filterForm = $('#notification_overview_filter');
        filterForm.on('click', '.field-filterform-categoryfilter label', function (evt) {
            if (evt.target.isSameNode(this)) {
                evt.preventDefault();
                const checkbox = $(this).children().first();
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
            const allSelected = filterForm.find('.field-filterform-categoryfilter input[type=checkbox]:not(:checked)').length === 0;
            filterForm.find('.field-filterform-allfilter input').prop('checked', allSelected);
            event.trigger('humhub:notification:filterApplied', filterForm);
        }).on('click', '.field-filterform-allfilter label', function () {
            const selectAll = $(this).find('input[type=checkbox]').prop('checked');
            filterForm.find('#filterform-categoryfilter input[type=checkbox]').prop('checked', selectAll);
            event.trigger('humhub:notification:filterApplied', filterForm);
        }).on('change', 'input[name="FilterForm[seenFilter]"]', function () {
            event.trigger('humhub:notification:filterApplied', filterForm);
        });
    };

    var initOverviewPage = function () {
        handleFilterChanges();
        if ($('#notification_overview_list').length) {
            OverviewWidget.instance('#notification_overview_list');
            if ($('#notification_overview_list li.new').length) {
                $('#notification_overview_markseen').show();
            }
        }
    };

    module.export({
        init: init,
        markAsSeen: markAsSeen,
        getNotificationCount: getNotificationCount,
        NotificationDropDown: NotificationDropDown,
        OverviewWidget: OverviewWidget
    });
});
