<?php

namespace Modules\CustomForm\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCustomFormEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param $messageData
     */
    public function __construct($messageData)
    {
        $this->data = $messageData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->subject($this->data['title']);

        foreach ($this->data as $key => $data) {

            if ($key != 'title') {
                if ($data['type'] == 'email' && $data['value'])
                    $subject->replyTo($data['value']);

                if ($data['type'] == 'file' && $data['value']) {
                    list($type, $data['value']) = explode(';', $data['value']);
                    list(, $data['value']) = explode(',', $data['value']);
                    $format = explode('/', $type)[1];
                    $fileName = $data['name'] ? $data['name'] : str_random() . '.' . $format;
                    $subject->attachData(base64_decode($data['value']), $fileName);

                    unset($this->data[$key]);
                }
            }
        }

        return $subject->markdown('customform::emails.feedback');
    }
}
