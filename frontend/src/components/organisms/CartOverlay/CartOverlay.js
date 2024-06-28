// Node Modules
import React, { Component } from 'react';

// Custom Modules
import AttributeSet from 'molecules/AttributeSet';
import { generateCompositeKey } from 'helpers/generateCompositeKey';

// Styles/CSS
import './CartOverlay.css';

class CartOverlay extends Component {
  constructor(props) {
    super(props);
    this.state = {
      quantity: {},
    };
  }

  componentDidMount() {
    this.setInitialQuantities();
  }

  componentDidUpdate(prevProps) {
    if (prevProps.items !== this.props.items) {
      this.setInitialQuantities();
    }
  }

  setInitialQuantities = () => {
    const initialQuantities = {};
    this.props.items.forEach(item => {
      const key = generateCompositeKey(item.id, item.selectedAttributes);
      initialQuantities[key] = item.quantity || 1;
    });
    this.setState({ quantity: initialQuantities });
  };

  incrementQuantity = (id, attributes) => {
    const key = generateCompositeKey(id, attributes);
    this.setState(prevState => ({
      quantity: {
        ...prevState.quantity,
        [key]: prevState.quantity[key] ? prevState.quantity[key] + 1 : 1,
      },
    }));
  };

  decrementQuantity = (id, attributes) => {
    const key = generateCompositeKey(id, attributes);
    this.setState(prevState => ({
      quantity: {
        ...prevState.quantity,
        [key]: prevState.quantity[key] && prevState.quantity[key] > 0 ? prevState.quantity[key] - 1 : 0,
      },
    }));
  };

  convertAttributeSet(selectedAttributes) {
    const attributeSets = [];

    Object.entries(selectedAttributes).forEach(([attributeType, attributeValue]) => {
      const attributeSet = {
        id: attributeType,
        name: attributeType,
        type: attributeType === "Color" ? "swatch" : "text",
        __typename: "AttributeSet",
        items: [{
          id: attributeType,
          value: attributeValue,
          displayValue: attributeValue,
          __typename: "Attribute"
        }]
      };

      attributeSets.push(attributeSet);
    });

    return attributeSets;
  }

  render() {
    const { items } = this.props;
    const totalQuantity = Object.values(this.state.quantity).reduce((acc, curr) => acc + curr, 0);
    const itemText = totalQuantity.length === 1 ? 'item' : 'items';

    return (
      <div className="cart-overlay">
        <h2>My bag, {totalQuantity} {itemText}</h2>
        {this.props.items.map((item, index) => {
          return (
            <div key={index} className="product-row">
              <div className="product-info-block">
                <div className="product-title">{item.title}</div>
                <div className="product-price">{item.price}</div>
                <div className="attribute-sets">
                  <AttributeSet
                    productId={item.id}
                  />
                </div>
              </div>
              <div className="quantity-control">
                <button className="quantity-btn" onClick={() => this.incrementQuantity(item.id)}>+</button>
                <div className="quantity">{this.state.quantity[item.id]}</div>
                <button className="quantity-btn" onClick={() => this.decrementQuantity(item.id)}>-</button>
              </div>
              <div className="image-block">
                <img src={item.image} alt={item.title} />
              </div>
            </div>
          )
        })}
      </div>
    );
  }
}

export default CartOverlay;