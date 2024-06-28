// Node Modules
import React, { Component } from 'react';
import { withRouter } from 'react-router-dom/cjs/react-router-dom.min';

// Custom Modules
import ImageGallery from 'organisms/ImageGallery';
import AttributeSet from 'molecules/AttributeSet';
import PriceTag from 'molecules/PriceTag';
import Button from 'atoms/Button';
import withCart from 'hoc/withCart';
import { withApolloClient } from 'hoc/withApolloClient';
import { GET_PRODUCT_DETAILS } from 'graphql/product/getProductDetails';
import { parseHtmlString } from 'helpers/parseHtmlString';
import { getPriceInCurrency } from 'helpers/productHelpers';

// Styles/CSS
import './ProductDetailPage.css';

class ProductDetailPage extends Component {
  constructor(props) {
    super(props);
    this.state = {
      productDetails: null,
      selectedImageIndex: 0,
      selectedAttributes: {},
      canAddToCart: true
    }

    this.setSelectedImageIndex = this.setSelectedImageIndex.bind(this);
  }

  componentDidMount() {
    const { productId } = this.props.match.params;
    const { apolloClient } = this.props;

    apolloClient
      .query({
        query: GET_PRODUCT_DETAILS,
        variables: { id: productId },
      })
      .then(result => this.setState({ productDetails: result.data.product }))
      .catch(error => console.error("Error fetching attributes:", error));

    this.selectDefaultAttributes();
  }

  selectDefaultAttributes = () => {
    if (!this.state.productDetails) {
      return;
    }

    const defaultSelectedAttributes = this.state.productDetails.attributes.reduce((acc, attributeSet) => {
      if (attributeSet.items && attributeSet.items.length > 0) {
        acc[attributeSet.id] = attributeSet.items[0].value;
      }
      return acc;
    }, {});

    this.setState({ selectedAttributes: defaultSelectedAttributes }, this.updateAddToCartStatus);
  }

  handleSelectAttribute = (attributeSets) => {
    const { attributeType, attributeValue } = attributeSets;
    this.setState(prevState => ({
      selectedAttributes: {
        ...prevState.selectedAttributes,
        attributeSets,
      },
    }), this.updateAddToCartStatus);
  }

  handleSelectImage(index) {
    this.setState({ selectedImageIndex: index });
  }

  updateAddToCartStatus = () => {
    if (!this.state.productDetails) {
      return;
    }

    const { attributes } = this.state.productDetails;
    const allAttributesSelected = attributes.every(attr => this.state.selectedAttributes.hasOwnProperty(attr.id));
    this.setState({ canAddToCart: allAttributesSelected });
  }

  addToCart = () => {
    const { productDetails, selectedAttributes } = this.state;
    const payload = {
      id: productDetails.id,
      title: productDetails.title,
      image: productDetails.images[0],
      price: productDetails.price,
      selectedAttributes: selectedAttributes,
    };

    this.props.addToCart(payload);

    console.log('Adding to cart:', payload);
  }

  formatPrice(price) {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
  }

  setSelectedImageIndex(index) {
    this.setState({ selectedImageIndex: index });
  }

  render() {
    const { productDetails, selectedImageIndex, canAddToCart } = this.state;
    if (!productDetails) return <div>Loading...</div>;

    return (
      <div className='page'>
        <div className='page-container'>
          <div className='second-line-wrapper'>
            <ImageGallery
              images={productDetails.gallery}
              selectedImageIndex={selectedImageIndex}
              setSelectedImageIndex={this.setSelectedImageIndex}
            />
            <div className='product-info'>
              <h1 className='product-heading'>{productDetails.name}</h1>
              <AttributeSet
                productId={productDetails.id}
                onAttributeSelect={this.handleSelectAttribute}
              />
              <PriceTag value={this.formatPrice(getPriceInCurrency(productDetails))} />
              <Button
                className='add-button'
                label="Add to Cart"
                onClick={this.addToCart}
              />
              <div className='product-description'>{parseHtmlString(productDetails.description)}</div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default
  withRouter(
    withApolloClient(
      withCart(
        ProductDetailPage
      )
    )
  );