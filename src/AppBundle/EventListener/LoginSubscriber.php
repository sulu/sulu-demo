<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * Add template to response when url is '/admin/login'.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if ('/admin/login' === $request->getPathInfo()) {
            $event->getResponse()->setContent(
                str_replace(
                    '</body>',
                    $this->getTemplate() . '</body>',
                    $event->getResponse()->getContent()
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.response' => 'onKernelResponse'
        );
    }

    /**
     * Returns the template with the injected javascript code.
     *
     * @return string
     */
    protected function getTemplate()
    {
        return '<script type="text/javascript">
                    var checkExist = setInterval(function() {
                        if ($(\'#username\').length) {
                            $(\'#username\').val(\'admin\');
                            $(\'#password\').val(\'admin\');
                            clearInterval(checkExist);
                        }
                    }, 100);
                </script>';
    }
}
