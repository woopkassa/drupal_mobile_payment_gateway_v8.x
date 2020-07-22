<?php

namespace Drupal\wooppay_mobile\Plugin\Commerce\PaymentGateway;

/**
 * Provides the Wooppay Mobile onsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "wooppay_mobile_onsite_checkout",
 *   label = @Translation("Wooppay Mobile (On-site)"),
 *   display_label = @Translation("Wooppay Mobile"),
 *    forms = {
 *    "add-payment-method" = "Drupal\wooppay_mobile\PluginForm\Onsite\PaymentMethodAddForm",
 *    "edit-payment-method" = "Drupal\commerce_payment\PluginForm\PaymentMethodEditForm",
 *   },
 * )
 */
class Onsite extends OnsitePaymentGatewayBase implements OnsiteInterface {

	public function defaultConfiguration()
	{
		return [
				'api_url' => 'https://www.test.wooppay.com/api/wsdl',
				'api_username' => '',
				'api_password' => '',
				'service_name' => '',
				'order_prefix' => '',
			] + parent::defaultConfiguration();
	}

	public function buildConfigurationForm(array $form, FormStateInterface $form_state)
	{
		$form = parent::buildConfigurationForm($form, $form_state);

		$form['api_url'] = [
			'#type' => 'textfield',
			'#title' => $this->t('API URL'),
			'#description' => $this->t('This is api url from the Wooppay manager.'),
			'#default_value' => $this->configuration['api_url'],
			'#required' => TRUE,
		];

		$form['api_username'] = [
			'#type' => 'textfield',
			'#title' => $this->t('API Username'),
			'#description' => $this->t('This is api username from tbe Wooppay manager.'),
			'#default_value' => $this->configuration['api_username'],
			'#required' => TRUE,
		];

		$form['api_password'] = [
			'#type' => 'textfield',
			'#title' => $this->t('API Password'),
			'#description' => $this->t('This is api password from tbe Wooppay manager.'),
			'#default_value' => $this->configuration['api_password'],
			'#required' => TRUE,
		];

		$form['service_name'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Service Name'),
			'#description' => $this->t('This is service name from tbe Wooppay manager.'),
			'#default_value' => $this->configuration['service_name'],
			'#required' => TRUE,
		];

		$form['order_prefix'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Order Prefix'),
			'#description' => $this->t('You can customize your order from this site by prefix.'),
			'#default_value' => $this->configuration['order_prefix'],
			'#required' => TRUE,
		];

		return $form;
	}

	public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
	{
		parent::submitConfigurationForm($form, $form_state);
		$values = $form_state->getValue($form['#parents']);

		$this->configuration['api_url'] = $values['api_url'];
		$this->configuration['api_username'] = $values['api_username'];
		$this->configuration['api_password'] = $values['api_password'];
		$this->configuration['service_name'] = $values['service_name'];
		$this->configuration['order_prefix'] = $values['order_prefix'];
	}

	public function createPayment(PaymentInterface $payment, $capture = TRUE) {
		$this->assertPaymentState($payment, ['new']);
		$payment_method = $payment->getPaymentMethod();
		$this->assertPaymentMethod($payment_method);
		$amount = $payment->getAmount();

		// Perform verifications related to billing address, payment currency, etc.
		// Throw exceptions as needed.
		// See \Drupal\commerce_payment\Exception for the available exceptions.

		// Perform the create payment request here, throw an exception if it fails.
		// Remember to take into account $capture when performing the request.
		$payment_method_token = $payment_method->getRemoteId();
		// The remote ID returned by the request.
		$remote_id = '123456';

		$next_state = $capture ? 'completed' : 'authorization';
		$payment->setState($next_state);
		$payment->setRemoteId($remote_id);
		$payment->save();
	}
}