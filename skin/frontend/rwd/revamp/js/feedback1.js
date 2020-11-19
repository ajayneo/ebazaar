    var formId = 'feedback-form';
    var myForm = new VarienForm(formId, true);
    var postUrl = $(formId).readAttribute('action');

    Validation.add('validate-captch-class','Captcha doesn\'t match!',function(the_field_value){
        return ValidCaptcha();
    });

    function doAjax() {
        if (myForm.validator.validate()) {
            new Ajax.Updater(
                { success:'feedback-form-message' }, postUrl, {
                    method:'post',
                    asynchronous:true,
                    evalScripts:false,
                    onComplete:function(request, json) {
                        //Element.hide(formId);
                        document.getElementById("feedback-form").reset();
                        Element.show('feedback-form-message');
                        Element.hide('feedback-form-loader');
                        DrawCaptcha();
                    },
                    onLoading:function(request, json){
                        Element.show('feedback-form-loader');
                    },
                    parameters: $(formId).serialize(true)
                }
            );
        }
    }

    new Event.observe(formId, 'submit', function(e){
        e.stop();
        doAjax();
    });

    //Created / Generates the captcha function
    function DrawCaptcha()
    {
        var a = Math.ceil(Math.random() * 9)+ '';
        var b = Math.ceil(Math.random() * 9)+ '';
        var c = Math.ceil(Math.random() * 9)+ '';
        var d = Math.ceil(Math.random() * 9)+ '';
        //var e = Math.ceil(Math.random() * 10)+ '';
        //var f = Math.ceil(Math.random() * 10)+ '';
        //var g = Math.ceil(Math.random() * 10)+ '';
        //var code = a + ' ' + b + ' ' + ' ' + c + ' ' + d + ' ' + e + ' '+ f + ' ' + g;
        var code = a + ' ' + b + ' ' + ' ' + c + ' ' + d;
        jQuery("#feedback_txtCaptcha").html(code);
        //document.getElementById("txtCaptcha").value = code
    }

    // Validate the Entered input aganist the generated security code function
    function ValidCaptcha(){
        var str1 = removeSpaces(jQuery('#feedback_txtCaptcha').html());
        var str2 = removeSpaces(jQuery('#feedback_verification_code').val());
        if (str1 == str2) return true;
        return false;

    }

    // Remove the spaces from the entered and generated code
    function removeSpaces(string)
    {
        return string.split(' ').join('');
    }
    jQuery(document).ready(function(){
        DrawCaptcha();
    });