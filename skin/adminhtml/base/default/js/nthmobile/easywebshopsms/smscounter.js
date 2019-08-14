var myEasywebshopsmsCounter = Class.create({
    initialize: function(eventToObserve) // Is called when the page has finished loading by the Event.observe code below
    {

        var easywebshopsmsCounter = document.getElementById('easywebshopsmsCounter');
        var easywebshopsmsCounterContainer = document.getElementById('easywebshopsmsCounterContainer');
        var maxchars = 160;
        var activeTextArea = false;
        var textAreaId = false;

        $('easywebshopsms_message_configuration').observe(eventToObserve, function(event) {
            var textlength = 0;
            activeTextArea = event.findElement('textarea');

            if (activeTextArea) {

                if (textAreaId !== activeTextArea.id) {
                    easywebshopsmsCounterContainer.remove(); //remove old easywebshopsmsCounterContainer
                    activeTextArea.insert({//reinitialize conterContainer in new position
                        after: easywebshopsmsCounterContainer
                    });
                    easywebshopsmsCounterContainer.show(); //snow easywebshopsmsCounter div at starts
                }
                textlength = activeTextArea.value.length;

                easywebshopsmsCounter.update(textlength);

                if (textlength <= (maxchars - 50)) {
                    $('easywebshopsmsCounter').setStyle({
                        fontSize: '150%',
                        fontWeight: 'normal',
                        color: '#0F910F'
                    });
                    $('easywebshopsmsTooLongAlert').hide();
                }
                else if (textlength <= (maxchars - 20)) {
                    $('easywebshopsmsCounter').setStyle({
                        fontWeight: 'bold',
                        color: '#FF8400'
                    });
                    $('easywebshopsmsTooLongAlert').hide();
                } else {
                    $('easywebshopsmsCounter').setStyle({
                        fontWeight: ' bold',
                        color: '#B80000'
                    });
                    $('easywebshopsmsTooLongAlert').show();
                }
            }
        });
    }
});
// Global variable for the instance of the class
// Creating an instance of the class if the page has finished loading
Event.observe(window, 'load', function() {
    document.getElementById('easywebshopsmsCounterContainer').hide(); //hide easywebshopsmsCounter div at start
    new myEasywebshopsmsCounter('click');
    new myEasywebshopsmsCounter('keyup');
});