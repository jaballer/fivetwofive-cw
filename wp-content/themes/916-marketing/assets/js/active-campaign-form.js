/**
 * ActiveCampaign embed script for form 5 ("Contact Us"), copied verbatim from
 * the ActiveCampaign form export. Validates the form, submits it to
 * 916marketing.activehosted.com/proc.php (JSONP), and shows the thank-you or
 * error message ActiveCampaign returns.
 *
 * Depends on the markup contract in template-parts/active-campaign-form.php:
 * the #_form_5_ / #_form_5_submit ids, ._form-content, ._form-thank-you, and
 * the field names. Replace this file wholesale when re-exporting the form.
 */
window.cfields = {"3":"test_dropdown","2":"contact_form_message"};
window._show_thank_you = function(id, message, trackcmp_url, email) {
    var form = document.getElementById('_form_' + id + '_'), thank_you = form.querySelector('._form-thank-you');
    form.querySelector('._form-content').style.display = 'none';
    thank_you.innerHTML = message;
    thank_you.style.display = 'block';
    const vgoAlias = typeof visitorGlobalObjectAlias === 'undefined' ? 'vgo' : visitorGlobalObjectAlias;
    var visitorObject = window[vgoAlias];
    if (email && typeof visitorObject !== 'undefined') {
        visitorObject('setEmail', email);
        visitorObject('update');
    } else if (typeof(trackcmp_url) != 'undefined' && trackcmp_url) {
        // Site tracking URL to use after inline form submission.
        _load_script(trackcmp_url);
    }
    if (typeof window._form_callback !== 'undefined') window._form_callback(id);
    thank_you.setAttribute('tabindex', '-1');
    thank_you.focus();
};
window._show_unsubscribe = function(id, message, trackcmp_url, email) {
    var form = document.getElementById('_form_' + id + '_'), unsub = form.querySelector('._form-thank-you');
    var branding = form.querySelector('._form-branding');
    if (branding) {
        branding.style.display = 'none';
    }
    form.querySelector('._form-content').style.display = 'none';
    unsub.style.display = 'block';
    form.insertAdjacentHTML('afterend', message)
    const vgoAlias = typeof visitorGlobalObjectAlias === 'undefined' ? 'vgo' : visitorGlobalObjectAlias;
    var visitorObject = window[vgoAlias];
    if (email && typeof visitorObject !== 'undefined') {
        visitorObject('setEmail', email);
        visitorObject('update');
    } else if (typeof(trackcmp_url) != 'undefined' && trackcmp_url) {
        // Site tracking URL to use after inline form submission.
        _load_script(trackcmp_url);
    }
    if (typeof window._form_callback !== 'undefined') window._form_callback(id);
};
window._show_error = function(id, message, html) {
    var form = document.getElementById('_form_' + id + '_'),
        err = document.createElement('div'),
        button = form.querySelector('button[type="submit"]'),
        old_error = form.querySelector('._form_error');
    if (old_error) old_error.parentNode.removeChild(old_error);
    err.innerHTML = message;
    err.className = '_error-inner _form_error _no_arrow';
    var wrapper = document.createElement('div');
    wrapper.className = '_form-inner _show_be_error';
    wrapper.appendChild(err);
    button.parentNode.insertBefore(wrapper, button);
    var submitButton = form.querySelector('[id^="_form"][id$="_submit"]');
    submitButton.disabled = false;
    submitButton.classList.remove('processing');
    if (html) {
        var div = document.createElement('div');
        div.className = '_error-html';
        div.innerHTML = html;
        err.appendChild(div);
    }
};
window._show_pc_confirmation = function(id, header, detail, show, email) {
    var form = document.getElementById('_form_' + id + '_'), pc_confirmation = form.querySelector('._form-pc-confirmation');
    if (pc_confirmation.style.display === 'none') {
        form.querySelector('._form-content').style.display = 'none';
        pc_confirmation.innerHTML = "<div class='_form-title'>" + header + "</div>" + "<p>" + detail + "</p>" +
            "<button class='_submit' id='hideButton'>Manage preferences</button>";
        pc_confirmation.style.display = 'block';
        var mp = document.querySelector('input[name="mp"]');
        mp.value = '0';
    } else {
        form.querySelector('._form-content').style.display = 'inline';
        pc_confirmation.style.display = 'none';
    }

    var hideButton = document.getElementById('hideButton');
    // Add event listener to the button
    hideButton.addEventListener('click', function() {
        var submitButton = document.querySelector('#_form_5_submit');
        submitButton.disabled = false;
        submitButton.classList.remove('processing');
        var mp = document.querySelector('input[name="mp"]');
        mp.value = '1';
        const cacheBuster = new URL(window.location.href);
        cacheBuster.searchParams.set('v', new Date().getTime());
        window.location.href = cacheBuster.toString();
    });

    const vgoAlias = typeof visitorGlobalObjectAlias === 'undefined' ? 'vgo' : visitorGlobalObjectAlias;
    var visitorObject = window[vgoAlias];
    if (email && typeof visitorObject !== 'undefined') {
        visitorObject('setEmail', email);
        visitorObject('update');
    } else if (typeof(trackcmp_url) != 'undefined' && trackcmp_url) {
        // Site tracking URL to use after inline form submission.
        _load_script(trackcmp_url);
    }
    if (typeof window._form_callback !== 'undefined') window._form_callback(id);
};
window._load_script = function(url, callback, isSubmit) {
    var head = document.querySelector('head'), script = document.createElement('script'), r = false;
    var submitButton = document.querySelector('#_form_5_submit');
    script.charset = 'utf-8';
    script.src = url;
    if (callback) {
        script.onload = script.onreadystatechange = function() {
            if (!r && (!this.readyState || this.readyState == 'complete')) {
                r = true;
                callback();
            }
        };
    }
    script.onerror = function() {
        if (isSubmit) {
            if (script.src.length > 10000) {
                _show_error("5", "Sorry, your submission failed. Please shorten your responses and try again.");
            } else {
                _show_error("5", "Sorry, your submission failed. Please try again.");
            }
            submitButton.disabled = false;
            submitButton.classList.remove('processing');
        }
    }

    head.appendChild(script);
};
(function() {
    var iti;
    if (window.location.search.search("excludeform") !== -1) return false;
    var getCookie = function(name) {
        var match = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]+)'));
        return match ? match[2] : localStorage.getItem(name);
    }
    var setCookie = function(name, value) {
        var now = new Date();
        var time = now.getTime();
        var expireTime = time + 1000 * 60 * 60 * 24 * 365;
        now.setTime(expireTime);
        document.cookie = name + '=' + value + '; expires=' + now + ';path=/; Secure; SameSite=Lax;';
        localStorage.setItem(name, value);
    }
    var addEvent = function(element, event, func) {
        if (element.addEventListener) {
            element.addEventListener(event, func);
        } else {
            var oldFunc = element['on' + event];
            element['on' + event] = function() {
                oldFunc.apply(this, arguments);
                func.apply(this, arguments);
            };
        }
    }
    var _removed = false;
    var form_to_submit = document.getElementById('_form_5_');
    var allInputs = form_to_submit.querySelectorAll('input, select, textarea'), tooltips = [], submitted = false;

    var getUrlParam = function(name) {
        if (name.toLowerCase() !== 'email') {
            var params = new URLSearchParams(window.location.search);
            return params.get(name) || false;
        }
        // email is a special case because a plus is valid in the email address
        var qString = window.location.search;
        if (!qString) {
            return false;
        }
        var parameters = qString.substr(1).split('&');
        for (var i = 0; i < parameters.length; i++) {
            var parameter = parameters[i].split('=');
            if (parameter[0].toLowerCase() === 'email') {
                return parameter[1] === undefined ? true : decodeURIComponent(parameter[1]);
            }
        }
        return false;
    };

    var acctDateFormat = "%m/%d/%Y";
    var getNormalizedDate = function(date, acctFormat) {
        var decodedDate = decodeURIComponent(date);
        if (acctFormat && acctFormat.match(/(%d|%e).*%m/gi) !== null) {
            return decodedDate.replace(/(\d{2}).*(\d{2}).*(\d{4})/g, '$3-$2-$1');
        } else if (Date.parse(decodedDate)) {
            var dateObj = new Date(decodedDate);
            var year = dateObj.getFullYear();
            var month = dateObj.getMonth() + 1;
            var day = dateObj.getDate();
            return `${year}-${month < 10 ? `0${month}` : month}-${day < 10 ? `0${day}` : day}`;
        }
        return false;
    };

    var getNormalizedTime = function(time) {
        var hour, minutes;
        var decodedTime = decodeURIComponent(time);
        var timeParts = Array.from(decodedTime.matchAll(/(\d{1,2}):(\d{1,2})\W*([AaPp][Mm])?/gm))[0];
        if (timeParts[3]) { // 12 hour format
            var isPM = timeParts[3].toLowerCase() === 'pm';
            if (isPM) {
                hour = parseInt(timeParts[1]) === 12 ? '12' : `${parseInt(timeParts[1]) + 12}`;
            } else {
                hour = parseInt(timeParts[1]) === 12 ? '0' : timeParts[1];
            }
        } else { // 24 hour format
            hour = timeParts[1];
        }
        var normalizedHour = parseInt(hour) < 10 ? `0${parseInt(hour)}` : hour;
        var minutes = timeParts[2];
        return `${normalizedHour}:${minutes}`;
    };

    for (var i = 0; i < allInputs.length; i++) {
        var regexStr = "field\\[(\\d+)\\]";
        var results = new RegExp(regexStr).exec(allInputs[i].name);
        if (results != undefined) {
            allInputs[i].dataset.name = allInputs[i].name.match(/\[time\]$/)
                ? `${window.cfields[results[1]]}_time`
                : window.cfields[results[1]];
        } else {
            allInputs[i].dataset.name = allInputs[i].name;
        }
        var fieldVal = getUrlParam(allInputs[i].dataset.name);

        if (fieldVal) {
            if (allInputs[i].dataset.autofill === "false") {
                continue;
            }
            if (allInputs[i].type == "radio" || allInputs[i].type == "checkbox") {
                if (allInputs[i].value == fieldVal) {
                    allInputs[i].checked = true;
                }
            } else if (allInputs[i].type == "date") {
                allInputs[i].value = getNormalizedDate(fieldVal, acctDateFormat);
            } else if (allInputs[i].type == "time") {
                allInputs[i].value = getNormalizedTime(fieldVal);
            } else {
                allInputs[i].value = fieldVal;
            }
        }
    }

    var remove_tooltips = function() {
        for (var i = 0; i < tooltips.length; i++) {
            tooltips[i].tip.parentNode.removeChild(tooltips[i].tip);
        }
        tooltips = [];
    };
    var remove_tooltip = function(elem) {
        for (var i = 0; i < tooltips.length; i++) {
            if (tooltips[i].elem === elem) {
                tooltips[i].tip.parentNode.removeChild(tooltips[i].tip);
                tooltips.splice(i, 1);
                return;
            }
        }
    };
    var create_tooltip = function(elem, text) {
        var tooltip = document.createElement('div'),
            arrow = document.createElement('div'),
            inner = document.createElement('div'), new_tooltip = {};
        tooltip.id = `${elem.id}-error`;
        tooltip.setAttribute('role', 'alert')
        if (elem.type != 'radio' && (elem.type != 'checkbox' || elem.name === 'sms_consent')) {
            tooltip.className = '_error';
            arrow.className = '_error-arrow';
            inner.className = '_error-inner';
            inner.innerHTML = text;
            tooltip.appendChild(arrow);
            tooltip.appendChild(inner);
            elem.parentNode.appendChild(tooltip);
        } else {
            tooltip.className = '_error-inner _no_arrow';
            tooltip.innerHTML = text;
            elem.parentNode.insertBefore(tooltip, elem);
            new_tooltip.no_arrow = true;
        }
        new_tooltip.tip = tooltip;
        new_tooltip.elem = elem;
        tooltips.push(new_tooltip);
        return new_tooltip;
    };
    var resize_tooltip = function(tooltip) {
        var rect = tooltip.elem.getBoundingClientRect();
        var doc = document.documentElement,
            scrollPosition = rect.top - ((window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0));
        if (scrollPosition < 40) {
            tooltip.tip.className = tooltip.tip.className.replace(/ ?(_above|_below) ?/g, '') + ' _below';
        } else {
            tooltip.tip.className = tooltip.tip.className.replace(/ ?(_above|_below) ?/g, '') + ' _above';
        }
    };
    var resize_tooltips = function() {
        if (_removed) return;
        for (var i = 0; i < tooltips.length; i++) {
            if (!tooltips[i].no_arrow) resize_tooltip(tooltips[i]);
        }
    };
    var validate_field = function(elem, remove) {
        var tooltip = null, value = elem.value, no_error = true;
        remove ? remove_tooltip(elem) : false;
        elem.removeAttribute('aria-invalid');
        elem.removeAttribute('aria-describedby');
        if (elem.type != 'checkbox') elem.className = elem.className.replace(/ ?_has_error ?/g, '');
        if (elem.getAttribute('required') !== null) {
            if (elem.type == 'radio' || (elem.type == 'checkbox' && /any/.test(elem.className))) {
                var elems = form_to_submit.elements[elem.name];
                if (!(elems instanceof NodeList || elems instanceof HTMLCollection) || elems.length <= 1) {
                    no_error = elem.checked;
                }
                else {
                    no_error = false;
                    for (var i = 0; i < elems.length; i++) {
                        if (elems[i].checked) no_error = true;
                    }
                }
                if (!no_error) {
                    tooltip = create_tooltip(elem, "Please select an option.");
                }
            } else if (elem.type =='checkbox') {
                var elems = form_to_submit.elements[elem.name], found = false, err = [];
                no_error = true;
                for (var i = 0; i < elems.length; i++) {
                    if (elems[i].getAttribute('required') === null) continue;
                    if (!found && elems[i] !== elem) return true;
                    found = true;
                    elems[i].className = elems[i].className.replace(/ ?_has_error ?/g, '');
                    if (!elems[i].checked) {
                        no_error = false;
                        elems[i].className = elems[i].className + ' _has_error';
                        err.push("Checking %s is required".replace("%s", elems[i].value));
                    }
                }
                if (!no_error) {
                    tooltip = create_tooltip(elem, err.join('<br/>'));
                }
            } else if (elem.tagName == 'SELECT') {
                var selected = true;
                if (elem.multiple) {
                    selected = false;
                    for (var i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].selected) {
                            selected = true;
                            break;
                        }
                    }
                } else {
                    for (var i = 0; i < elem.options.length; i++) {
                        if (elem.options[i].selected
                            && (!elem.options[i].value
                                || (elem.options[i].value.match(/\n/g)))
                        ) {
                            selected = false;
                        }
                    }
                }
                if (!selected) {
                    elem.className = elem.className + ' _has_error';
                    no_error = false;
                    tooltip = create_tooltip(elem, "Please select an option.");
                }
            } else if (value === undefined || value === null || value === '') {
                elem.className = elem.className + ' _has_error';
                no_error = false;
                tooltip = create_tooltip(elem, "This field is required.");
            }
        }
        if (no_error && elem.name == 'email') {
            if (!value.match(/^[\+_a-z0-9-'&=]+(\.[\+_a-z0-9-']+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i)) {
                elem.className = elem.className + ' _has_error';
                no_error = false;
                tooltip = create_tooltip(elem, "Enter a valid email address.");
            }
        }
        if (no_error && (elem.id == 'phone')) {
            if (elem.value.trim() && typeof iti != 'undefined' && !iti.isValidNumber()) {
                elem.className = elem.className + ' _has_error';
                no_error = false;
                tooltip = create_tooltip(elem, "Enter a valid phone number.");
            }
        }
        if (no_error && /date_field/.test(elem.className)) {
            if (!value.match(/^\d\d\d\d-\d\d-\d\d$/)) {
                elem.className = elem.className + ' _has_error';
                no_error = false;
                tooltip = create_tooltip(elem, "Enter a valid date.");
            }
        }

        if (no_error && elem.name === 'sms_consent') {
            const showError = !!elem.attributes.required && !elem.checked;
            if (showError) {
                elem.className = elem.className + ' _has_error';
                no_error = false;
                tooltip = create_tooltip(elem, "Please check this box to continue");
            } else {
                elem.className = elem.className.replace(/ ?_has_error ?/g, '');
            }
        }
        tooltip ? resize_tooltip(tooltip) : false;
        if (!no_error && elem.hasAttribute('id')) {
            elem.setAttribute('aria-invalid', 'true');
            elem.setAttribute('aria-describedby', `${elem.id}-error`);
        }
        return no_error;
    };
    var needs_validate = function(el) {
        if(el.getAttribute('required') !== null){
            return true;
        }
        if((el.name === 'email' || el.name === 'phone' || el.id === 'sms_consent') && el.value !== ""){
            return true;
        }

        return false;
    };
    var validate_email_or_phone_required = function() {
        var emailInput = form_to_submit.querySelector('input[name="email"]');
        var phoneInput = form_to_submit.querySelector('input[name="phone"]');
        if (!emailInput && !phoneInput) {
            return true;
        }
        var hasEmailValue = emailInput && emailInput.value.trim() !== '';
        var hasPhoneValue = phoneInput && phoneInput.value.trim() !== '';
        if (hasEmailValue || hasPhoneValue) {
            return true;
        }
        var errorField = emailInput || phoneInput;
        errorField.className = errorField.className.replace(/ ?_has_error ?/g, '') + ' _has_error';
        var tooltip = create_tooltip(
            errorField,
            "Forms must have an email field or a phone field present"
        );
        resize_tooltip(tooltip);
        errorField.setAttribute('aria-invalid', 'true');
        if (errorField.hasAttribute('id')) {
            errorField.setAttribute('aria-describedby', errorField.id + '-error');
        }
        return false;
    };
    var validate_form = function(e) {
        var err = form_to_submit.querySelector('._form_error'), no_error = true;
        if (!submitted) {
            submitted = true;
            for (var i = 0, len = allInputs.length; i < len; i++) {
                var input = allInputs[i];
                if (needs_validate(input)) {
                    if (input.type == 'text' || input.type == 'number' || input.type == 'time' || input.type == 'tel') {
                        addEvent(input, 'blur', function() {
                            this.value = this.value.trim();
                            validate_field(this, true);
                        });
                        addEvent(input, 'input', function() {
                            validate_field(this, true);
                        });
                    } else if (input.type == 'radio' || input.type == 'checkbox') {
                        (function(el) {
                            function getElementsArray(name){
                                const value =  form_to_submit.elements[name];
                                if (Array.isArray(value)){
                                    return value;
                                }
                                return [value];
                            }
                            var radios = getElementsArray(el.name);
                            for (var i = 0; i < radios.length; i++) {
                                addEvent(radios[i], 'change', function() {
                                    validate_field(el, true);
                                });
                            }
                        })(input);
                    } else if (input.tagName == 'SELECT') {
                        addEvent(input, 'change', function() {
                            validate_field(this, true);
                        });
                    } else if (input.type == 'textarea'){
                        addEvent(input, 'input', function() {
                            validate_field(this, true);
                        });
                    }
                }
            }
        }
        remove_tooltips();
        for (var i = 0, len = allInputs.length; i < len; i++) {
            var elem = allInputs[i];
            if (needs_validate(elem)) {
                if (elem.tagName.toLowerCase() !== "select") {
                    elem.value = elem.value.trim();
                }
                validate_field(elem) ? true : no_error = false;
            }
        }
        if (!validate_email_or_phone_required()) {
            no_error = false;
        }
        if (!no_error && e) {
            e.preventDefault();
        }
        if (!no_error) {
            const firstFocusableError = form_to_submit.querySelector('._has_error:not([disabled])');
            if (firstFocusableError && typeof firstFocusableError.focus === 'function') {
                firstFocusableError.focus();
            }
        }
        resize_tooltips();
        return no_error;
    };
    addEvent(window, 'resize', resize_tooltips);
    addEvent(window, 'scroll', resize_tooltips);

    var _form_serialize = function(form){if(!form||form.nodeName!=="FORM"){return }var i,j,q=[];for(i=0;i<form.elements.length;i++){if(form.elements[i].name===""){continue}switch(form.elements[i].nodeName){case"INPUT":switch(form.elements[i].type){case"tel":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].previousSibling.querySelector('div.iti__selected-dial-code').innerText)+encodeURIComponent(" ")+encodeURIComponent(form.elements[i].value));break;case"text":case"number":case"date":case"time":case"hidden":case"password":case"button":case"reset":case"submit":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"checkbox":case"radio":if(form.elements[i].checked){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value))}break;case"file":break}break;case"TEXTAREA":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"SELECT":switch(form.elements[i].type){case"select-one":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"select-multiple":for(j=0;j<form.elements[i].options.length;j++){if(form.elements[i].options[j].selected){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].options[j].value))}}break}break;case"BUTTON":switch(form.elements[i].type){case"reset":case"submit":case"button":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break}break}}return q.join("&")};

    const formSupportsPost = false;
    var form_submit = function(e) {

        e.preventDefault();
        if(form_to_submit.querySelector('input[name="phone"]') && typeof iti != 'undefined') {
            form_to_submit.querySelector('input[name="phone"]').value = iti.getNumber();
        }
        if (validate_form()) {
            // use this trick to get the submit button & disable it using plain javascript
            var submitButton = e.target.querySelector('#_form_5_submit');
            submitButton.disabled = true;
            submitButton.classList.add('processing');
            var serialized = _form_serialize(document.getElementById('_form_5_')).replace(/%0A/g, '\\n');
            var err = form_to_submit.querySelector('._form_error');
            err ? err.parentNode.removeChild(err) : false;
            async function submitForm() {
                var formData = new FormData();
                const searchParams = new URLSearchParams(serialized);
                searchParams.forEach((value, key) => {
                    if (key !== 'hideButton') {
                        formData.append(key, value);
                    }
                });
                let request = {
                    headers: {
                        "Accept": "application/json"
                    },
                    body: formData,
                    method: "POST"
                };

                let pageUrlParams = new URLSearchParams(window.location.search);
                if (pageUrlParams.has('t')) {
                    request.headers.Authorization = 'Bearer ' + pageUrlParams.get('t');
                }
                const response = await fetch('https://916marketing.activehosted.com/proc.php?jsonp=true', request);

                return response.json();
            }
            if (formSupportsPost) {
                submitForm().then((data) => {
                    eval(data.js);
                }).catch(() => {
                    _show_error("5", "Sorry, your submission failed. Please try again.");
                });
            } else {
                _load_script('https://916marketing.activehosted.com/proc.php?' + serialized + '&jsonp=true', null, true);
            }
        }
        return false;
    };
    addEvent(form_to_submit, 'submit', form_submit);
})();
