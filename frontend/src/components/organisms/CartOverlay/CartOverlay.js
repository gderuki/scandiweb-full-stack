import React, { Component } from 'react';

class CartOverlay extends Component {
  render() {
    const { items, onClose } = this.props;
    return (
      <div className="cart-overlay">
        Cart Items
      </div>
    );
  }
}

export default CartOverlay;