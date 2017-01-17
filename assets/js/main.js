$(document).ready(function () {
    
    //Choose product form product list (popup add item)
    $(document).on('click', '.order-shopaholic-offer-product-list tr', function () {
        var iProductID = $(this).attr('data-id');

        $('.modal-body').html('');
        $('.oc-shopaholic-loader').show();
        
        $.request('onGetProductData', {data: {product_id: iProductID}, success: function(data) {
            
            $('.oc-shopaholic-loader').hide();
            $('.modal-body').html(data.result);
        }});
    });
    
    //Add offer to order
    $(document).on('click', '.order-shopaholic-offer-add-to-order', function (e) {
        e.preventDefault();

        var iOfferID = $(this).attr('data-id'),
            iCount = $('.order-shopaholic-offer-count').val(),
            iOrderID = $('input[name="order_data[order_id]"]').val();

        $.request('onAddOffer', {data: {offer_id: iOfferID, count: iCount, order_id: iOrderID}, success: function(response) {
            
            var data = response.data;
            if(response.result) {

                $('#Orders-update-RelationController-offers-view').html(data['#Orders-update-RelationController-offers-view']);
                $('#Form-field-Order-price_block-group').html(data['#Form-field-Order-price_block-group']);

                $.oc.flashMsg({text: data.message, 'class': 'success', 'interval': 5});
            } else {
                $.oc.flashMsg({text: response.error, 'class': 'error', 'interval': 5});
            }
        }});
    });
    
    //Choose category (popup add item)
    $(document).on('click', '.order-shopaholic-offer-category-list tbody tr', function () {
        var iCategoryID = $(this).attr('data-id');

        $('.modal-body').html('');
        $('.oc-shopaholic-loader').show();
        
        $.request('onGetChildCategoryList', {data: {category_id: iCategoryID}, success: function(data) {
            
            $('.oc-shopaholic-loader').hide();
            $('.modal-body').html(data.result);
        }});
    });
    
    //Button "Back to parent category"
    $(document).on('click', '.order-shopaholic-offer-category-list-back', function (e) {
        e.preventDefault();

        var iCategoryID = $(this).attr('data-id');
        
        $('.modal-body').html('');
        $('.oc-shopaholic-loader').show();
        
        $.request('onGetChildCategoryList', {data: {category_id: iCategoryID}, success: function(data) {

            $('.oc-shopaholic-loader').hide();
            $('.modal-body').html(data.result);
        }});
    });

    /*################################### inputs - Choose product ############################################*/

    var _validation = false;
    var productPrice = 0;
    window.obProduct = {};

    $('body').on('change', '.b-cart-filter-line input', function() {

        var _pickBlocks = $('.b-cart-filter-line'),
            _pickLabel = _pickBlocks.find('label'),
            _pickInput = _pickBlocks.find('input');

        var _thisInput = $(this),
            _thisInputValue = _thisInput.val(),
            _thisProp = _thisInput.parents('[data-prop-id]'),
            _thisPropInput = _thisProp.find('input'),
            _thisPropId = _thisProp.attr('data-prop-id'),
            _thisPropSiblings = _thisProp.siblings('[data-prop-id]'),
            _varsBlock = $('.b-product-selection__offers');

        _validation = true;

        _pickLabel.addClass('_dis');


        var _pickResults =_varsBlock.find(`[data-property-id="${_thisPropId}"][data-property-value="${_thisInputValue}"]`),
            _pickResultsBlocks = _pickResults.parent(),
            _pickResultsTmp = _pickResults,
            _pickResultsBlocksTmp = _pickResultsBlocks;

        _thisProp.addClass('_act');

        //Снимаем с возможных вариантов _dis, добавляяя в фильтр новую выборку
        _thisPropSiblings.each(function() {
            var _thisPick = $(this),
                _pickId = _thisPick.attr('data-prop-id'),
                _pickInput = _thisPick.find('input');
            _thisPick.removeClass('_act');
            _pickInput.each(function() {

                var _this = $(this),
                    _thisValue = _this.val(),
                    _label = _this.parents('label');

                if(_pickResultsBlocks.find(`[data-property-id="${_pickId}"][data-property-value="${_thisValue}"]`).parent().length > 0) {

                    //Проверим наличие товара
                    var bHasQuantityTrue = false;
                    for(var i in _pickResultsBlocks.find(`[data-property-id="${_pickId}"][data-property-value="${_thisValue}"]`).parent()) {
                        if(_pickResultsBlocks.find(`[data-property-id="${_pickId}"][data-property-value="${_thisValue}"]`).parent().eq(i).attr('data-offer-quantity') > 0) {
                            bHasQuantityTrue = true;
                            break;
                        }
                    }

                    if(bHasQuantityTrue || _this.is(':checked')) {
                        _label.removeClass('_dis');
                        if(_this.is(':checked')) {
                            _pickResultsTmp = _pickResultsBlocks.find(`[data-property-id="${_pickId}"][data-property-value="${_thisValue}"]`);
                            _pickResultsBlocksTmp = _pickResultsTmp.parent();
                        }
                    }
                } else if(_this.is(':checked')) {
                    _this.prop('checked', false);
                }
            });

            _pickResults = _pickResultsTmp;
            _pickResultsBlocks = _pickResultsBlocksTmp;
        });

        var _allPickInput = _pickBlocks.find('input:checked');

        //Еще раз проходимся по всем свойствам, но уже с полным фильтром
        _thisPropSiblings.each(function() {
            var _thisPick = $(this),
                _pickId = _thisPick.attr('data-prop-id'),
                _pickInput = _thisPick.find('input');

            _pickResultsBlocks = _varsBlock.children();
            _allPickInput.each(function() {
                var _iPropertyID = $(this).parents('[data-prop-id]').attr('data-prop-id'),
                    _sValue = $(this).val();

                if(_iPropertyID != _pickId) {
                    _pickResultsBlocks = _pickResultsBlocks.find(`[data-property-id="${_iPropertyID}"][data-property-value="${_sValue}"]`).parent();
                }
            });

            _pickInput.each(function() {

                var _this = $(this),
                    _thisValue = _this.val(),
                    _label = _this.parents('label');

                if(_pickResultsBlocks.find(`[data-property-id="${_pickId}"][data-property-value="${_thisValue}"]`).parent().length == 0) {
                    _label.addClass('_dis');
                }
            });
        });

        _pickResultsBlocks = _varsBlock.children();

        //Соберем фильтр без активного свойства
        _thisPropSiblings.each(function() {
            var _thisPick = $(this),
                _pickId = _thisPick.attr('data-prop-id'),
                _pickInput = _thisPick.find('input:checked');

            if(_pickInput.length == 0) {
                return;
            }

            _pickResults = _pickResultsBlocks.find(`[data-property-id="${_pickId}"][data-property-value="${_pickInput.val()}"]`);
            _pickResultsBlocks = _pickResults.parent();
        });

        //Проходим по активному элементу и делаем активными кнопки, которые подходят под фильтр
        _thisPropInput.each(function() {

            var _this = $(this),
                _thisValue = _this.val(),
                _label = _this.parents('label');

            if(_pickResultsBlocks.find(`[data-property-id="${_thisPropId}"][data-property-value="${_thisValue}"]`).parent().length > 0) {

                //Проверим наличие товара
                var bHasQuantityTrue = false;
                for(var i in _pickResultsBlocks.find(`[data-property-id="${_thisPropId}"][data-property-value="${_thisValue}"]`).parent()) {
                    if(_pickResultsBlocks.find(`[data-property-id="${_thisPropId}"][data-property-value="${_thisValue}"]`).parent().eq(i).attr('data-offer-quantity') > 0) {
                        bHasQuantityTrue = true;
                        break;
                    }
                }

                if(bHasQuantityTrue || _this.is(':checked')) {
                    _label.removeClass('_dis');
                }
            }
        });

        _pickBlocks.each(function() {
            var _thisBlock = $(this),
                _thisBlockId = _thisBlock.attr('data-prop-id'),
                _thisBlockInputsChecked = _thisBlock.find('input:checked');

            if(_thisBlockInputsChecked.length < 1) {
                _validation = false;
            }
        });

        /* ### After choose ### */

        $('.price-counter ._price').html(0);
        if(_validation) {
            var _result = _pickResultsBlocks.find(`[data-property-id="${_thisPropId}"][data-property-value="${_thisInputValue}"]`).parent(),
                _price = parseFloat(_result.attr('data-offer-price')),
                _count = parseInt($('.goods-count').val()),
                _quantity = parseInt(_result.attr('data-offer-quantity'));

            $('.b-goods-popup__img').attr('src', _result.attr('data-offer-images'));
            $('.order-shopaholic-offer-add-to-order').attr('data-id', _result.attr('data-offer-id'));
            $('.order-shopaholic-offer-quantity span').html(_quantity);
            $('.order-shopaholic-offer-code').html(_result.attr('data-offer-code'));

            productPrice = _price;
            
            if(_quantity > 0) {
                $('.order-shopaholic-offer-add-to-order').removeAttr('disabled');
                $('.goods-count').attr('data-max-count', _quantity);
            } else {
                if($('.order-shopaholic-check-quantity').val() == 1) {
                    $('.order-shopaholic-offer-add-to-order').attr('disabled', true);
                }
            }

            getPrice(productPrice, _count);
        } else {
            $('.order-shopaholic-offer-add-to-order').attr('data-id', '');
        }

    });

    $('body').on('keyup', '.goods-count', function() {
        var _this = $(this),
            _count = parseInt(_this.val()),
            _quantuty = parseInt(_this.attr('data-max-count'));

        if(!_validation) {
            return;
        }

        if(_count < 1 || !_count) {
            _count = 1;
        }

        getPrice(productPrice, _count);
    });

    $('body').on('blur', '.goods-count', function() {

        var _this = $(this),
            _count = parseInt(_this.val()),
            _quantuty = parseInt(_this.attr('data-max-count'));

        if(_count < 1 || !_count) {
            _count = 1;
            _this.val(_count);
        }
        
        if(_count > _quantuty) {
            _this.val(_quantuty);
            _count = _quantuty;
        }

        getPrice(productPrice, _count);
    });


    /**
     * Получаем цену
     * @param _price
     * @param _count
     */
    function getPrice(_price, _count) {
        $('.price-counter ._price').html(formatPrice(_price * _count));
    }

    function formatPrice(_price) {
        
        var dPrice = parseFloat(_price).toFixed($('.order-shopaholic-default-decimals').val());
        return dPrice.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
    }

    /**
     * Update delivery price value
     */
    $(document).on('keyup', '#Form-field-Order-shipping_price', function () {
        
        var sDeliveryPrice = $(this).val(),
            dPrice = 0;
        
        sDeliveryPrice = parseFloat(sDeliveryPrice.replace(/[^0-9\.]+/g, ''));
        
        if(sDeliveryPrice > 0) {
            dPrice = sDeliveryPrice;
        }
        
        $('input[name="order_data[shipping_price]"]').val(dPrice);
        $('.order-shopaholic-delivery-price').html(formatPrice(dPrice));
        
        updateTotalPrice();
    });
    
    $(document).on('keyup', '.order-shopaholic-offer-list-count', function () {
        updateOfferTotalPrice();
    });
    
    $(document).on('click', '.order-apply-price-button', function () {
        
        var dPrice = $(this).attr('data-price'),
            _this = $(this);

        _this.hide();

        _this.siblings('input').val(dPrice);
        _this.parents('tr').find('.order-shopaholic-offer-list-count').attr('data-price', dPrice);
        _this.parents('tr').find('.list-cell-name-pivot-price').html(formatPrice(dPrice));
        
        updateOfferTotalPrice();
    });

    /**
     * Update offer total price value
     */
    function updateOfferTotalPrice() {

        var iOfferTotalPrice = 0;
        $('.order-shopaholic-offer-list-count').each(function () {

            var iCount = parseInt($(this).val()),
                dPrice = parseFloat($(this).attr('data-price'));

            if(iCount > 0) {
                iOfferTotalPrice += dPrice * iCount;
            }

        });

        $('input[name="order_data[offers_total_price]"]').val(iOfferTotalPrice);
        $('.order-shopaholic-offers-total-price').html(formatPrice(iOfferTotalPrice));
        
        updateTotalPrice();
    }

    /**
     * Update total price value
     */
    function updateTotalPrice() {
        
        var dOffersPrice = parseFloat($('input[name="order_data[offers_total_price]"]').val()),
            dDeliveryPrice = parseFloat($('input[name="order_data[shipping_price]"]').val()),
            dTotalPrice = dOffersPrice + dDeliveryPrice;
        
        $('input[name="order_data[total_price]"]').val(dTotalPrice);
        $('.order-shopaholic-total-price').html(formatPrice(dTotalPrice));
    }
    
    /* ################### Search user #################### */
    
    var iLastTimeSearch = 0,
        sCurrentSearch = '',
        _data;
    
    $('body').on('keyup', '.search-group-input', function(e) {
        var _this = $(this),
            _thisVal = _this.val().trim(),
            _dropdown = _this.siblings('.form__label__dopdown-search-group');

        iLastTimeSearch = new Date().getTime();
        
        if(e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 13) {
            return;
        }
        
        if(!_thisVal || _thisVal.length < 3) {
            return;
        }

        getSearchResult(_this, _thisVal, _dropdown);
    });

    function getSearchResult (_this, sSearch, _dropdown) {
        
        setTimeout(function() {
            var iCurrentTime = new Date().getTime();

            if(iCurrentTime >= iLastTimeSearch + 300) {
                sCurrentSearch = sSearch;

                $.request('onSearchUser', {data: {search: sSearch}, success: function(response) {

                    if(response.value != sCurrentSearch) {
                        return;
                    }

                    _data = response.data;
                    var _dropdown = _this.siblings('.form__label__dopdown-search-group');
                    
                    _dropdown.html('');
                    for(var i = 0; i < _data.length; i++) {
                        _dropdown.append(
                            '<div class="form__label__dopdown-search-group__i">' +
                            '<div class="form__label__dopdown-search-group__i__name">' + _data[i].name + '</div>' +
                            '<div class="form__label__dopdown-search-group__i__phone">' + _data[i].phone.join(', ') + '</div>' +
                            '</div>'
                        );
                    }

                    var _dropdownItems = _dropdown.children('.form__label__dopdown-search-group__i');
                    if(sSearch.trim().length >= 3 && _dropdownItems.length > 0) {
                        _dropdown.slideDown(0);
                    } else {
                        _dropdown.slideUp(0);
                    }
                }});
            }
        }, 300);
    }

    $('body').on('keydown', '.search-group-input', function(e) {
        var _this = $(this),
            _dropdown = _this.siblings('.form__label__dopdown-search-group'),
            _dropdownItems = _dropdown.children('.form__label__dopdown-search-group__i'),
            _counter = _dropdown.children('.form__label__dopdown-search-group__i._hover').index();

        if(e.keyCode == 38) {
            e.preventDefault();
            _dropdownItems.eq(_counter - 1).addClass('_hover').siblings('.form__label__dopdown-search-group__i').removeClass('_hover');
        }

        if(e.keyCode == 40) {
            e.preventDefault();
            if(_dropdownItems.length - 1 == _counter) {
                _counter = -1;
            }
            _dropdownItems.eq(_counter + 1).addClass('_hover').siblings('.form__label__dopdown-search-group__i').removeClass('_hover');
        }

        if(e.keyCode == 13) {
            _dropdown.stop().slideUp(0, function() {
                var _input = _dropdown.find('.form__label__dopdown-search-group__i._hover');
                inputsFilling(_input, _dropdown);
            });
        }
    });

    $('body').on('mouseenter', '.form__label__dopdown-search-group__i', function() {
        var _this = $(this);
        _this.addClass('_hover').siblings('.form__label__dopdown-search-group__i').removeClass('_hover');
    });

    $('body').on('mousedown', '.form__label__dopdown-search-group__i', function() {
        var _this = $(this),
            _parents = _this.parents('.form__label__dopdown-search-group');

        _parents.stop().slideUp(0, function() {
            inputsFilling(_this, _parents);
        });
    });
    
    $(document).on('change', 'select[name="Order[phone_list]"]', function () {
        $('input[name="Order[phone]"]').val($('select[name="Order[phone_list]"]').val());
    });

    /**
     * Fill inputs and selects
     * @param _var
     * @param drop
     */
    function inputsFilling(_var, drop) {
        
        if(!_data[_var.index()] || _data[_var.index()].phone == undefined || !_data[_var.index()].phone) {
            return;
        }
        
        var _arPhones = _data[_var.index()].phone;

        $('input[name="Order[user_name_search]"]').val('');
        $('input[name="Order[user_search]"]').val(_data[_var.index()].id);
        $('input[name="Order[name]"]').val(_data[_var.index()].name);
        $('input[name="Order[email]"]').val(_data[_var.index()].email);

        $('select[name="Order[phone_list]"]').html('');

        var _name = $('.search-group-input').val().trim().replace(/\D/g, ''),
            _bSelected = false;
        if(_name.length > 3) {
            for(var i = 0; i < _arPhones.length; i++) {
                if(_arPhones[i].replace(/\D/g, '').indexOf(_name) > -1) {
                    $('select[name="Order[phone_list]"]').append('<option value="' + _arPhones[i] + '" selected>' + _arPhones[i] + '</option>');
                    $('select[name="Order[phone_list]"]').siblings('span').find('.select2-selection__rendered').html(_arPhones[i]);
                    _bSelected = true;
                    continue;
                }
                $('select[name="Order[phone_list]"]').append('<option value="' + _arPhones[i] + '">' + _arPhones[i] + '</option>');
            }

        } else {
            for(var i = 0; i < _arPhones.length; i++) {
                $('select[name="Order[phone_list]"]').append('<option value="' + _arPhones[i] + '">' + _arPhones[i] + '</option>');
            }
        }

        if(!_bSelected && !!_arPhones[0]) {
            $('select[name="Order[phone_list]"]').find('option').eq(0).attr('selected', true);
            $('select[name="Order[phone_list]"]').siblings('span').find('.select2-selection__rendered').html(_arPhones[0]);
        }

        $('input[name="Order[phone]"]').val($('select[name="Order[phone_list]"]').val());

        $('div[data-field-name="user_address_list"]').hide();
    }
});