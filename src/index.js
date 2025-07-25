const { registerPaymentMethod } = window.wc.wcBlocksRegistry; 

const QWICPAY_NAME = 'qwicpay';
const QWICPAY_TITLE = 'QwicPay';
const QWICPAY_DESCRIPTION = 'Pay securely via QwicPay';
const QWICPAY_ICON =  null
const QWICPAY_SUPPORTS_FEATURES = ['products', 'blocks']; 

const QwicPayContent = () => {
    return wp.element.createElement(
        'p',
        null,
        QWICPAY_DESCRIPTION
    );
};

const QwicPayLabel = () => {
    return wp.element.createElement(
        'div',
        null,
        wp.element.createElement('span', null, QWICPAY_TITLE),
        // Add an icon if you want!
        QWICPAY_ICON && wp.element.createElement('img', { src: QWICPAY_ICON, alt: QWICPAY_TITLE, style: { height: '20px', marginLeft: '5px', verticalAlign: 'middle' } })
    );
};

registerPaymentMethod( {
    name: QWICPAY_NAME,
    label: wp.element.createElement(QwicPayLabel, null),
    content: wp.element.createElement(QwicPayContent, null),
    edit: wp.element.createElement(QwicPayContent, null),
    canMakePayment: () => true,
    ariaLabel: QWICPAY_TITLE,
    supports: {
        features: QWICPAY_SUPPORTS_FEATURES,
    },
} );