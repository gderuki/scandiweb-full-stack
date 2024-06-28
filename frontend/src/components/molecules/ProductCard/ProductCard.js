// Node modules
import React from 'react';
import { withRouter, Link } from 'react-router-dom';

// Custom Modules
import ProductTitle from 'atoms/ProductTitle';
import ProductPrice from 'atoms/ProductPrice';
import ProductImage from 'atoms/ProductImage';
import Button from 'atoms/Button';
import AddToCartIcon from 'icons/AddToCardIcon';

// Styles/CSS
import './ProductCard.css';

class ProductCard extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isHovered: false,
    };
  }

  navigateToProductDetail = (productId, productSlug) => {
    this.props.history.push(`/product/${productId}/${productSlug}`);
  };

  setIsHovered = (isHovered) => {
    this.setState({ isHovered });
  }

  render() {
    const { productId, productSlug, title, price, imageUrl } = this.props;
    const { isHovered } = this.state;

    return (
      <Link
        to={`/product/${productId}/${productSlug}`}
        className="product-card"
        tabIndex="0"
        state={{ productId }}
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
        <ProductImage className='product-image' imageUrl={imageUrl} altText={title} />
        <ProductTitle title={title} />
        <ProductPrice price={price} />
      </Link>
    );
  }
}

export default withRouter(ProductCard);