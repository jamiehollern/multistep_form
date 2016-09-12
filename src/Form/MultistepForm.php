<?php

namespace Drupal\multistep_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MultistepForm.
 *
 * @package Drupal\multistep_form\Form
 */
class MultistepForm extends FormBase {

  /**
   * The number of steps in this form.
   *
   * @var int
   */
  protected $formSteps = 3;

  /**
   * The current form step, starts at 1.
   *
   * @var int
   */
  protected $formStep = 1;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $default_values = $form_state->get('values_step_' . $this->formStep);
    if ($this->formStep == 1) {
      $form['input_1'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Input 1'),
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => isset($default_values['input_1']) ? $default_values['input_1'] : '',
      ];
    }
    if ($this->formStep == 2) {
      $form['input_2'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Input 2'),
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => isset($default_values['input_2']) ? $default_values['input_2'] : '',
      ];
    }
    if ($this->formStep == 3) {
      $form['input_3'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Input 3'),
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => isset($default_values['input_3']) ? $default_values['input_3'] : '',
      ];
    }
    if ($this->formStep > 1) {
      $form['previous'] = [
        '#type' => 'submit',
        '#name' => 'prev',
        '#value' => t('Previous'),
        '#weight' => 0,
        '#limit_validation_errors' => [],
      ];
    }
    $submit_value = $this->formStep == $this->formSteps ? t('Submit') : t('Next');
    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => $submit_value,
      '#weight' => 1,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // We need to know what button was pressed so we know in which direction
    // to go and what to do in each case.
    $button = '';
    $triggering_element = $form_state->getTriggeringElement();
    if (isset($triggering_element['#name'])) {
      $button = $triggering_element['#name'];
    }
    // If it's the submit/next button, store the values and increment the step.
    if ($button == 'submit') {
      $form_state->cleanValues();
      $values = $form_state->getValues();
      $form_state->set('values_step_' . $this->formStep, $values);
      $this->formStep++;
    }
    // If it's the previous button, just decrement the step.
    if ($button == 'prev') {
      $this->formStep--;
    }
    // If we're still going through the form, rebuild it to display the
    // appropriate step.
    if ($this->formStep <= $this->formSteps) {
      $form_state->setRebuild();
    }
    // If we've reached the end of the form and there are no more steps, get all
    // of the values we've stored into an array.
    else {
      $values = [];
      foreach ($form_state->getStorage() as $key => $content) {
        if (strpos($key, 'values_step_') === 0) {
          foreach ($content as $name => $value) {
            $values[$name] = $value;
          }
        }
      }
      // Do something with the values, set a message, redirect or whatever.
    }
  }

}
