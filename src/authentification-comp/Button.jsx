import React from "react";
import styles from "./Button.module.css";

const Button = ({
  children,
  onClick,
  type = "button",
  disabled = false,
  loading = false,
  variant = "primary",
}) => {
  const getVariantClass = () => {
    switch (variant) {
      case "secondary":
        return styles.btnSecondary;
      case "danger":
        return styles.btnDanger;
      default:
        return styles.btnPrimary;
    }
  };

  return (
    <button
      type={type}
      onClick={onClick}
      disabled={disabled || loading}
      className={`${styles.btn} ${getVariantClass()} ${
        loading ? styles.btnLoading : ""
      }`}
    >
      {loading ? "Loading..." : children}
    </button>
  );
};

export default Button;