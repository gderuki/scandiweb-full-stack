// Node Modules
import React, { Component } from 'react';

// Custom Modules
import AttributeSet from 'molecules/AttributeSet';
import { extractProductIdFromCompositeKey } from 'helpers/generateCompositeKey';

// Styles/CSS
import './CartOverlay.css';

class CartOverlay extends Component {
  constructor(props) {
    super(props);
    this.state = {
      quantity: {},
      noclick: false,
    };
  }

  render() {
    const { items } = this.props;
    const totalQuantity = items.length;
    const itemText = items.length === 1 ? 'item' : 'items';

    return (
      <div className="cart-overlay">
        <h2 className='cart-heading'>My bag, {totalQuantity} {itemText}</h2>
        <div className="products-list">
          {items.map((item, index) => (
            <React.Fragment key={index}>
              <div className="product-row">
                <div className="product-info-block">
                  <div className="product-title">{item.title}</div>
                  <div className="product-price">{item.price}</div>
                  <div className="attribute-sets">
                    <AttributeSet
                      small
                      noClick
                      productId={extractProductIdFromCompositeKey(item.id)}
                      selectedAttributes={item.selectedAttributes}
                    />
                  </div>
                </div>
                <div className="quantity-control">
                  <button className="quantity-btn" onClick={() => this.props.addToCart(item)}>+</button>
                  <div className="quantity">{item.quantity}</div>
                  <button className="quantity-btn" onClick={() => this.props.removeFromCart(item.id)}>-</button>
                </div>
                <div className="image-block">
                  <img src={item.image} alt={item.title} />
                </div>
              </div>
              {index !== this.props.items.length - 1 && <hr className='product-delimiter' />}
            </React.Fragment>
          ))}
        </div>
      </div>
    );
  }
}

export default CartOverlay;