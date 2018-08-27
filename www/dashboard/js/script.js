
$(document).ready(function(){
    
    // update now
    updateValues();

    // start interval   
    setInterval(updateValues, 60000);
    
    
});


function updateValues(){
    // iterate over all data labels and get their values
    let values = [];
    $("[data-variable]").each(function(){
        const key = $(this).data("variable");
        if(!values.includes(key))
            values.push(key);
    });

    // construct api-call
    const url = "../api/data/" + values.join(":");


    // do ajax get request
    $.get(url, function(data){
        if(data.success && data.data){ 
            for(var name in data.data){
                key = data.data[name];
                console.log(key);

                // put data (allow for prefix and suffix data too)
                $("[data-variable=" + name + "]").each(function(){
                    let text = key.value;

                    const prefix = $(this).data("prefix");
                    const suffix = $(this).data("suffix");

                    if(prefix) text = prefix + text;
                    if(suffix) text = text + suffix;
                    
                    $(this).text(text);
                });

                // "updated XX ago" text
                const agoText = "updated " + moment.unix(key.timestamp_update).fromNow();

                $("[data-timestamp=" + name + "]").text(agoText);
            }        
        }
    });
}