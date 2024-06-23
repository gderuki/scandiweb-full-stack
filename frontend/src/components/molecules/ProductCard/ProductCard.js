import React from 'react';
import ProductTitle from 'components/atoms/ProductTitle';
import ProductPrice from 'components/atoms/ProductPrice';
import ProductImage from 'components/atoms/ProductImage';
import Button from 'components/atoms/Button';
import './ProductCard.css';

class ProductCard extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isHovered: false,
      isClicked: false
    };
  }

  setIsHovered = (isHovered) => {
    this.setState({ isHovered });
  }

  render() {
    const { productId, title, price, imageUrl } = this.props;
    const { isHovered } = this.state;

    return (
      <div
        role="button"
        aria-label="Add to cart"
        tabIndex="0"
        label="Add to Cart"
        onMouseEnter={() => this.setIsHovered(true)}
        onMouseLeave={() => this.setIsHovered(false)}
        className="productCardStyle"
        onKeyDown={(event) => {
          if (event.key === 'Enter') {
            console.log('Added to cart via keyboard: ', productId);
            this.setState({ isClicked: true });
            setTimeout(() => this.setState({ isClicked: false }), 100);
          }
        }}
      >
        <Button
          role="button"
          aria-label="Add to cart"
          tabIndex="0"
          label="Add to Cart"
          onClick={() => {
            console.log('Added to cart: ', productId);
            this.setState({ isClicked: true });
            setTimeout(() => this.setState({ isClicked: false }), 100);
          }}
          style={{
            cursor: 'pointer',
            padding: '4px',
            backgroundColor: this.state.isClicked ? '#f0f0f0' : 'transparent',
            border: '1px solid #e9e9e9',
            borderRadius: 0,
            position: 'absolute',
            bottom: '16px',
            right: '16px',
            display: isHovered ? 'block' : 'none'
          }}
          onFocus={(e) => e.target.style.outline = '2px solid blue'}
          onBlur={(e) => e.target.style.outline = 'none'}
        />
        <div aria-live="polite" className="visually-hidden">
          {this.state.isClicked ? 'Item added to cart' : ''}
        </div>
        <ProductImage imageUrl={imageUrl} altText={title} />
        <ProductTitle title={title} />
        <ProductPrice price={price} />
      </div>
    );
  }
}

export default ProductCard;