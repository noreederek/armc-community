<?php



namespace humhub\components\rendering;

/**
 * MailRenderer extends the DefaultViewPathRenderer with a renderText method.
 *
 * The $defaultTextView and/or $defaultTextViewPath can be set to define a fallback
 * view or search view path.
 *
 * @author buddha
 * @since 1.2
 */
class MailRenderer extends DefaultViewPathRenderer
{
    /**
     * @inheritdoc
     */
    public $subPath = 'mails';

    /**
     * @var string fallback text view.
     */
    public $defaultTextView;

    /**
     * @var string fallback text view path.
     */
    public $defaultTextViewPath;

    /**
     * Renders the text mail content for the given $viewable.
     *
     * @param \humhub\components\rendering\Viewable $viewable
     * @param array $params
     * @return string
     */
    public function renderText(Viewable $viewable, $params = [])
    {
        $textRenderer = new static([
            'subPath' => 'mails/plaintext',
            'parent' => $this->parent,
            'defaultView' => $this->defaultTextView,
            'defaultViewPath' => $this->defaultTextViewPath,
        ]);

        return html_entity_decode(strip_tags($textRenderer->render($viewable, $params)));
    }
}
