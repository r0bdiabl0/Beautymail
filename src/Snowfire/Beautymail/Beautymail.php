<?php

namespace Snowfire\Beautymail;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Request;

abstract class Beautymail implements MailerContract
{
    /**
     * Contains settings for emails processed by Beautymail.
     *
     * @var array
     */
    private $settings;

    /**
     * The mailer contract depended upon.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    private $mailer;

    /**
     * Initialise the settings and mailer.
     *
     * @param array $settings
     * @param \Illuminate\Contracts\Mail\Mailer $mailer
     */
    public function __construct(array $settings, MailerContract $mailer)
    {
        $this->settings = $settings;
        $this->mailer = $mailer;
        $this->setLogoPath();
    }

    /**
     * Send a new message immediately using a view.
     *
     * @param string|array $view
     * @param array $data
     * @param \Closure|string|null $callback
     *
     * @return void
     */
    abstract public function sendNow($view, array $data = [], $callback = null);

    public function to($users)
    {
        return (new PendingMail($this->mailer))->to($users);
    }

    public function bcc($users)
    {
        return (new PendingMail($this->mailer))->bcc($users);
    }

    public function cc($users)
    {
        return (new PendingMail($this->mailer))->cc($users);
    }

    /**
     * Retrieve the settings.
     *
     * @return array
     */
    public function getData()
    {
        return $this->settings;
    }

    /**
     * @return \Illuminate\Contracts\Mail\Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Send a new message using a view.
     *
     * @param string|array $view
     * @param array $data
     * @param \Closure|string|null $callback
     *
     * @return void
     */
    public function send($view, array $data = [], $callback = null)
    {
        $data = array_merge($this->settings, $data);

        $this->mailer->send($view, $data, $callback);
    }

    /**
     * @param string|array $view
     * @param array $data
     *
     * @return \Illuminate\View\View
     */
    public function view($view, array $data = [])
    {
        $data = array_merge($this->settings, $data);

        return view($view, $data);
    }

    /**
     * Send a new message when only a raw text part.
     *
     * @param string $text
     * @param mixed $callback
     *
     * @return void
     */
    public function raw($text, $callback)
    {
        return $this->mailer->send(['raw' => $text], [], $callback);
    }

    /**
     * @return void
     */
    private function setLogoPath()
    {
        $this->settings['logo']['path'] = str_replace(
            '%PUBLIC%',
            Request::getSchemeAndHttpHost(),
            $this->settings['logo']['path']
        );
    }
}
