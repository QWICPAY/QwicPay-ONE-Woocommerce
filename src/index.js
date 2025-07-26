const { registerPaymentMethod } = window.wc.wcBlocksRegistry;

const QwicPayData = window.wc.wcSettings.getPaymentMethodData('qwicpay')


const QWICPAY_SUPPORTS_FEATURES = QwicPayData.supports

const QwicPayContent = () => {
  return wp.element.createElement(
    "div",
    {
      style: {
        background: "#f7faff",
        padding: "16px",
        borderRadius: "8px",
        border: "1px solid #dce9f9",
        fontSize: "14px",
        color: "#333",
        lineHeight: "1.5",
        fontFamily: "'Instrument Sans', sans-serif"
      }
    },
    wp.element.createElement("p", null, QwicPayData.description),
    wp.element.createElement("p", null, "QwicPay lets you pay quickly and securely with your saved card or device. It’s fast, easy, and built for trust."),
    wp.element.createElement(
      "ul",
      { style: { paddingLeft: "20px", margin: "8px 0 0" } },
      wp.element.createElement("li", null, "PCI-DSS Level 1 certified security"),
      wp.element.createElement("li", null, "One-click payments with saved cards"),
      wp.element.createElement("li", null, "Apple Pay & Samsung Pay ready")
    )
  );
};

const QwicPayLabel = () => {
  return wp.element.createElement(
    "div",
    {
      style: {
        display: "flex",
        alignItems: "center",
        gap: "12px"
      }
    },
    // Gradient circle with icon
    QwicPayData.icon &&
      wp.element.createElement(
        "div",
        {
          style: {
            background: "radial-gradient(circle at center, #007BFF 0%, #f3f3f3ff 100%)",
            borderRadius: "50%",
            padding: "8px",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "44px",
            height: "44px",
            boxShadow: "0 2px 6px rgba(0,0,0,0.1)"
          }
        },
        wp.element.createElement("img", {
          src: QwicPayData.icon,
          alt: QwicPayData.title + " Payment method",
          style: {
            height: "24px",
            width: "24px",
            objectFit: "contain"
          }
        })
      ),
    // Label text
    wp.element.createElement(
      "span",
      {
        style: {
          fontSize: "16px",
          fontWeight: "600",
          color: "#1a1a1a",
          fontFamily: "'Instrument Sans', sans-serif"
        }
      },
      QwicPayData.title
    )
  );
};


registerPaymentMethod({
  name: QwicPayData.name,
  label: wp.element.createElement(QwicPayLabel, null),
  content: wp.element.createElement(QwicPayContent, null),
  edit: wp.element.createElement(QwicPayContent, null),
  canMakePayment: () => true,
  ariaLabel: QwicPayData.title,
  supports: {
    features: QWICPAY_SUPPORTS_FEATURES,
  },
});
