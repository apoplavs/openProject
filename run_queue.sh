
# check if queue:work started, if no, start it
QUEUE_STATUS=$(ps aux | grep "queue:work")
if $( echo $QUEUE_STATUS | grep --quiet 'artisan queue:work')
then
    exit;
else
    php artisan queue:work
fi