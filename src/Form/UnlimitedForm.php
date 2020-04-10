<?php

namespace Drupal\unlimited_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class UnlimitedForm extends ConfigFormBase
{
    /**
     * Config settings.
     *
     * @var string
     */
    const SETTINGS = 'unlimited.settings';

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            static::SETTINGS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'unlimited_field';
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config(static::SETTINGS);
        $form['#tree'] = TRUE;
        $num_rows = $form_state->get('num_rows');
        $form['actions']['#type'] = 'actions';

        $form['book_row'] = [
            '#type' => 'details',
            '#title' => $this->t('Items'),
            '#prefix' => '<div id="book-row-wrapper">',
            '#suffix' => '</div>',
            //'#attributes' => array('class' => array('container-inline')),
            '#open' => TRUE,
        ];
        if (empty($num_rows)) {
            $num_rows = $form_state->set('num_rows', 1);
        }
        for ($i = 0; $i < $form_state->get('num_rows'); $i++) {
            $form['book_row']['book_content'][$i] = [
                '#type' => 'fieldset',
                '#title' => '#Item ' . ($i + 1),
                '#open' => TRUE,
            ];
            $form['book_row']['book_content'][$i]['candidate_name'][$i] = [
                '#type' => 'textfield',
                '#title' => $this->t('Name'),
                '#description' => $this->t('Enter the Name of the Candidate. Note that the Name must be at least 6 characters in length.'),
                '#required' => TRUE,
            ];
            $form['book_row']['book_content'][$i]['candidate_email'][$i] = [
                '#type' => 'email',
                '#title' => $this->t('Email'),
            ];

            $form['book_row']['book_content'][$i]['candidate_image'][$i] = [
                '#type' => 'managed_file',
                '#title' => $this->t('Image'),
                '#upload_location' => 'public://upload/profile',
            ];

            if ($i > 0) {
                $form['book_row']['book_content'][$i]['actions']['remove_name'][$i] = [
                    '#type' => 'submit',
                    '#value' => $this->t('Remove'),
                    '#submit' => array('::removeCallback'),
                    '#limit_validation_errors' => array(),
                    '#ajax' => [
                        'callback' => '::addmoreCallback',
                        'wrapper' => 'book-row-wrapper',
                    ],
                ];
            }
        }

        //if ($i == 0) {
        $form['book_row']['book_content'][$i]['actions']['add_row'] = [
            '#type' => 'submit',
            '#value' => $this->t('Add Item'),
            '#submit' => array('::addOne'),
            '#limit_validation_errors' => array(),
            '#ajax' => [
                'callback' => '::addmoreCallback',
                'wrapper' => 'book-row-wrapper',
            ],
        ];
        //}
        $form_state->setCached(FALSE);
        return parent::buildForm($form, $form_state);

    }

    /**
     * Callback for both ajax-enabled buttons.
     *
     * Selects and returns the fieldset with the names in it.
     */
    public function addmoreCallback(array &$form, FormStateInterface $form_state)
    {
        $name_field = $form_state->get('num_rows');
        return $form['book_row'];
    }

    /**
     * Submit handler for the "add-one-more" button.
     *
     * Increments the max counter and causes a rebuild.
     */
    public function addOne(array &$form, FormStateInterface $form_state)
    {
        $name_field = $form_state->get('num_rows');
        $add_button = $name_field + 1;
        $form_state->set('num_rows', $add_button);
        $form_state->setRebuild();
    }

    /**
     * Submit handler for the "remove one" button.
     *
     * Decrements the max counter and causes a form rebuild.
     */
    public function removeCallback(array &$form, FormStateInterface $form_state)
    {
        $name_field = $form_state->get('num_rows');
        if ($name_field > 1) {
            $remove_button = $name_field - 1;
            $form_state->set('num_rows', $remove_button);
        }
        $form_state->setRebuild();
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitForm($form, $form_state);
    }


}