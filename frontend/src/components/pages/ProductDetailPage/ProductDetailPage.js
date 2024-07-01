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
import { getImageUrl, getPriceInCurrency } from 'helpers/productHelpers';

// Styles/CSS
import './ProductDetailPage.css';
import { generateCompositeKey } from 'helpers/generateCompositeKey';

class ProductDetailPage extends Component {
  constructor(props) {
    super(props);
    this.state = {
      productDetails: null,
      attributeSets: null,
      selectedImageIndex: 0,
      selectedAttributes: [],
      canAddToCart: false
    }

    this.setSelectedImageIndex = this.setSelectedImageIndex.bind(this);
  }

  componentDidMount() {
    this.fetchProductDetails();
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
                onAllAttributesSelected={this.updateAddToCartStatus}
                onAttributeSelect={this.handleSelectAttribute}
              />
              <PriceTag value={this.formatPrice(getPriceInCurrency(productDetails))} />
              <Button
                className={`add-button ${!this.state.canAddToCart ? 'disabled' : ''}`}
                label="Add to Cart"
                onClick={this.addToCart}
                disabled={!this.state.canAddToCart}
              />
              <div className='product-description'>{parseHtmlString(productDetails.description)}</div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  fetchProductDetails = () => {
    const { productId } = this.props.match.params;
    const { apolloClient } = this.props;

    this.setState({ productDetails: null }, () => {
      apolloClient
        .query({
          query: GET_PRODUCT_DETAILS,
          variables: { id: productId },
        })
        .then(result => {
          this.setState({ productDetails: result.data.product });
        })
        .catch(error => {
          console.error("Error fetching data:", error);
          console.log("Error details:", error.message);
        });
    });
  };

  handleSelectAttribute = (attributeObject) => {
    if (!attributeObject) {
      return;
    }

    Object.entries(attributeObject).forEach(([key, value]) => {
      this.setState(prevState => {
        const attributeIndex = prevState.selectedAttributes.findIndex(attr => Object.keys(attr)[0] === key);

        let updatedAttributes;
        if (attributeIndex > -1) {
          updatedAttributes = [...prevState.selectedAttributes];
          updatedAttributes[attributeIndex] = { [key]: value };
        } else {
          updatedAttributes = [...prevState.selectedAttributes, { [key]: value }];
        }

        return { selectedAttributes: updatedAttributes };
      });
    });
  }

  handleSelectImage(index) {
    this.setState({ selectedImageIndex: index });
  }

  updateAddToCartStatus = (value) => {
    this.setState({ canAddToCart: value });
  }

  addToCart = () => {
    const { productDetails, selectedAttributes } = this.state;

    const payload = {
      id: generateCompositeKey(productDetails.id, selectedAttributes),
      title: productDetails.name,
      image: getImageUrl(productDetails),
      price: getPriceInCurrency(productDetails),
      selectedAttributes: selectedAttributes,
      quantity: 1,
    };

    this.props.addToCart(payload);
    this.props.toggleCartOverlay();
  }

  formatPrice(price) {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
  }

  setSelectedImageIndex(index) {
    this.setState({ selectedImageIndex: index });
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