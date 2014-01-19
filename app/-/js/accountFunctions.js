(function($) {
    $.fn.extend({
        accountFunctions: function(options) {

            var defaults = {
                loaded: false,
                menuContainer: '#header',
                listType: 'ol',
                listClass: null,
                listWrapper: null
            };

            var options = $.extend(defaults, options);

            return this.each(function() {
                new $.accountFunctions(this, options);
            });
        },
        contentManager: function(options) {

            var defaults = {
                loaded: false,
                defaultArticleType: '1',
                editingClass: 'editing',
                contentManagerContainer: '.contentManagerContainer',

                editContentName: '#editContentName',
                contentButtons: '.contentButtons',

                createArticleType: 'createArticleType',
                createArticleUrl: 'createArticleUrl',

                contentTitleInput: '#contentTitle',
                contentPageType: '#contentPageType',
                contentPageLabel: '#contentPageUrl',

                contentTypeInput: '#contentType',
                contentPageInput: '#contentPage'
            };

            var options = $.extend(defaults, options);

            return this.each(function() {
                new $.contentManager(this, options);
            });
        }
    });

    $.accountFunctions = function(div, options) {
        $.accountFunctions.loadMenu(options);

        if ($('#updateClient').length > 0) {
            $('#updateClient').clientImageManager({});
        }
    };

    $.accountFunctions.validateForm = function(options) {

    };

    $.contentManager = function(div, options) {

        $(options.contentTitleInput).bind('keyup', function(event) {
            updatePageLabel();
        })
        .bind('change', function(event){
            updatePageLabel()
        });

        $(options.contentButtons + ' input').bind('click', function(event) {
            var div = $(this).attr('data-value');

            if (typeof div !== 'undefined' && div !== false) {
                $(options.contentManagerContainer).hide();
                $('#' + div).show();

                return false;
            }
        });

        $(options.editContentName).bind('click', function(event) {
            if (!$(event.target).is('input')
                    && !$(this).hasClass(options.editingClass)) {
                var value = $.contentManager.pageUrl($(options.contentPageLabel).html());

                contentNameInput = $('<input />')
                    .attr('name', options.editContentName)
                    .attr('class', options.editContentName)
                    .attr('type', 'text')
                    .attr('size', 20)
                    .val(value)
                    .focusout(function(){
                        var newValue = $(this).val();
                        $(options.contentPageLabel).html(newValue);
                        $(options.contentPageInput).val(newValue);
                    });

                $(options.contentPageLabel).html(contentNameInput);

                contentNameInput.focus();
            }

            return false;
        });

        $(options.contentTypeInput).bind('change', function(event) {
            $('#createNewArticleType').remove();
            selected = $(options.contentTypeInput + ' option:selected');
            value = '';

            if ($(this).val() > 0) {
                if ($(this).val() !== options.defaultArticleType) {
                    value = selected.attr('data-value') + '/';
                }
            } else {
                if (selected.attr('id') == options.createArticleType) {
                    nameInput = $('<div />')
                    .html('Name: ')
                    .append(
                        $('<input />')
                        .keyup(function(event) {
                            var value = $.contentManager.pageUrl($(event.target).val());
                            $(options.contentPageType).html(value + '/');
                            $('.' + options.createArticleUrl).val(value);

                            e.preventDefault();
                            return false;
                        })
                        .attr('name', options.createArticleType)
                        .attr('class', options.createArticleType)
                        .attr('type', 'text')
                        .attr('size', 20)
                    );

                    urlInput = $('<div />')
                    .html('Url: ')
                    .append(
                        $('<input />')
                        .attr('name', options.createArticleUrl)
                        .attr('class', options.createArticleUrl)
                        .attr('type', 'text')
                        .attr('size', 20)
                    );

                    $(this).after(
                        $('<div />')
                        .attr('id', 'createNewArticleType')
                        .append(nameInput)
                        .append(urlInput)
                    );
                }
            }

            $(options.contentPageType).html(value);
        });

        function updatePageLabel() {
            var value = $.contentManager.pageUrl($(options.contentTitleInput).val());
            $(options.contentPageLabel).html(value);
            $(options.contentPageInput).val(value);
        }


        $(options.contentTitleInput).focus();
    };

    $.contentManager.pageUrl = function(url) {
        url = url.toLowerCase();

        accents = {a:/\u00e1/g,e:/u00e9/g,i:/\u00ed/g,o:/\u00f3/g,u:/\u00fa/g,n:/\u00f1/g};
        for (var i in accents) {
            url = url.replace(accents[i],i);
        }

        url = url.replace(/\s/g,'-');
        url = url.replace(/[^a-zA-Z0-9\-]/g,'');

        return url.toLowerCase();
    };

    $.accountFunctions.menuItem = function() {
        var menuItem = null,
            submenuItems = null;

        function create(value, text)
        {
            menuItem = $("<li />")
                .css('list-style', 'none')
                .html(
                    $("<a></a>")
                    .attr('href', value)
                    .html(text)
                );
        }

        function addLink(value, text){
            if (submenuItems == null) {
                submenuItems = $('<ul />').addClass('sublinks');
                menuItem.find('a').addClass('dropdown')
            }

            submenuItems.append(
                $("<li />")
                    .css('list-style', 'none')
                    .html(
                        $("<a></a>")
                        .attr('href', value)
                        .html(text)
                    )
            );
        }

        return {
            createMenuItem: function(value, text) {
                create(value, text);
            },
            addSubLink: function(value, text) {
                addLink(value, text);
            },
            getMenuItem: function() {
                return menuItem.append(submenuItems);
            }
        };
    };

    $.accountFunctions.loadMenu = function(options) {
        var $menuContainer = $(options.menuContainer);

        $.ajax({
          url: '/ajax/getMenuItems' ,
          dataType: 'json',
          type: 'POST',
          data: {getItems: true},
          success: function(data) {
            if(data == null)
                return;

            list = $('<' + options.listType + '></' + options.listType + '>');
            var lists = [];

            $.each(data, function(key, item) {
                if (item.parent > 0) {
                    if(typeof lists[item.parent] == 'undefined') {
                        lists[key] = $.accountFunctions.menuItem();
                        lists[key].createMenuItem(item.value, item.text);
                    } else {
                        lists[item.parent].addSubLink(item.value, item.text);
                    }
                } else {
                    lists[key] = $.accountFunctions.menuItem();
                    lists[key].createMenuItem(item.value, item.text);
                }
            });

            $.each(lists, function(key, listItem) {
                if (typeof listItem != 'undefined') {
                    list.append(listItem.getMenuItem());
                }
            });

            if (options.listClass !== null) {
                list.addClass(options.listClass);
            }

            if (options.listWrapper !== null) {
                list = options.listWrapper.append(list);
            }

            $menuContainer
                .css('height', '40px')
                .css('width', '100%')
                .html(list);

          }
        });
    };


})(jQuery);