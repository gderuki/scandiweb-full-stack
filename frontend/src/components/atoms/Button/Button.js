import React, { Component } from 'react';

// intentionally left without own styling
class Button extends Component {
  render() {
    const { label, onClick, icon, style, className, disabled, dataTestId } = this.props;
    const buttonProps = {
      onClick,
      style,
      className,
      disabled,
      ...(dataTestId && { 'data-testid': dataTestId })
    };

    return (
      <button {...buttonProps}>
        {icon && <span>{icon}</span>}
        {label && <span>{label}</span>}
      </button>
    );
  }
}

export default Button;