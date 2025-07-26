const { registerPaymentMethod } = window.wc.wcBlocksRegistry;

const QwicPayData = window.wc.wcSettings.getPaymentMethodData("qwicpay");

const QWICPAY_SUPPORTS_FEATURES = QwicPayData.supports;

const QwicPayContent = () => {
  return wp.element.createElement(
    "div",
    {
      style: {
        background:
          "radial-gradient(circle at right center, #ba9fe6ff 0%, #8bbff7ff 10%, #ffffffff 30%, #ffffff 100%)",
        padding: "16px",
        borderRadius: "8px",
        border: "1px solid #dce9f9",
        fontSize: "14px",
        color: "#333",
        lineHeight: "1.5",
        fontFamily: "'Instrument Sans', sans-serif",
      },
    },
    //wp.element.createElement("p", null, QwicPayData.description),
    wp.element.createElement("p", null, "QwicPay Instant Payment Ecosystem"),
    wp.element.createElement(
      "ul",
      { style: { paddingLeft: "20px", margin: "8px 0 0" } },
      wp.element.createElement("li", null, "Credit & Debit cards"),
      wp.element.createElement("li", null, "Apple Pay & Samsung Pay"),
      wp.element.createElement(
        "li",
        null,
        "Cards stored across any QwicPay merchant"
      )
    )
  );
};

const QwicPayLabel = () => {
  const paymentIcons = [
    "visa.svg",
    "mastercard.svg",
    "apple-pay.svg",
    "samsung-pay.svg",
  ];

  const iconBase = QwicPayData.iconBase;
  const iconStyle = {
    height: "30px",
    marginLeft: "8px",
    objectFit: "contain",
  };

  return wp.element.createElement(
    "div",
    {
      style: {
        display: "flex",
        alignItems: "center",
        justifyContent: "space-between",
        width: "100%",
      },
    },
    // Left section: icon + title
    wp.element.createElement(
      "div",
      {
        style: { display: "flex", alignItems: "center", gap: "12px", flex: 1 },
      },
      QwicPayData.icon &&
        wp.element.createElement("img", {
          src: QwicPayData.icon,
          alt: QwicPayData.title + " Payment method",
          style: {
            width: "24px",
            height:"24px",
            borderRadius: "50%",
            objectFit: "cover",
            boxShadow: "0 0px 6px rgba(0,0,0,0.1)",
          },
        }),
      wp.element.createElement(
        "span",
        {
          style: {
            fontSize: "16px",
            fontWeight: "600",
            color: "#1a1a1a",
            fontFamily: "'Instrument Sans', sans-serif",
          },
        },
        QwicPayData.title
      )
    ),
    // Right section: payment method icons
    wp.element.createElement(
      "div",
      { style: { display: "flex", alignItems: "center", gap: "8px" } },
      paymentIcons.map((filename, idx) =>
        wp.element.createElement("img", {
          key: idx,
          src: iconBase + filename,
          alt: filename.replace(".svg", ""),
          style: iconStyle,
        })
      )
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
