import React from 'react';
import ProductTitle from 'atoms/ProductTitle';
import ProductPrice from 'atoms/ProductPrice';
import ProductImage from 'atoms/ProductImage';
import Button from 'atoms/Button';
import './ProductCard.css';
import AddToCartIcon from 'icons/AddToCardIcon';
import { withRouter } from 'react-router-dom';

class ProductCard extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isHovered: false,
    };
  }

  navigateToProductDetail = (productId) => {
    this.props.history.push(`/product/${productId}`);
  };

  setIsHovered = (isHovered) => {
    this.setState({ isHovered });
  }

  render() {
    const { productId, title, price, imageUrl } = this.props;
    const { isHovered } = this.state;

    return (
      <div
        tabIndex="0"
        label="Add to Cart"
        onClick={() => this.navigateToProductDetail(this.props.productId)}
        onMouseEnter={() => this.setIsHovered(true)}
        onMouseLeave={() => this.setIsHovered(false)}
        className="product-card"
      >
        <Button
          className="add-to-cart-button"
          icon={<AddToCartIcon />}
          onClick={() => {
            this.setState({ isClicked: true });
            setTimeout(() => this.setState({ isClicked: false }), 100);
          }}
          style={{
            display: isHovered ? 'block' : 'none'
          }}
        />
        <ProductImage imageUrl={imageUrl} altText={title} />
        <ProductTitle title={title} />
        <ProductPrice price={price} />
      </div>
    );
  }
}

export default withRouter(ProductCard);