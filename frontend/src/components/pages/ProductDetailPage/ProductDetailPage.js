import React, { Component } from 'react';
import './ProductDetailPage.css';
import Thumbnails from 'components/molecules/Thumbnails';
import SquareImage from 'components/atoms/SquareImage';
import ImageGallery from 'components/organisms/ImageGallery/ImageGallery';

class ProductDetailPage extends Component {

  constructor(props) {
    super(props);
    this.state = {
      productDetails:
      {
        id: 1,
        category: 'kids',
        title: 'Apple AirTag',
        price: 19.99,
        images: [
          "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg",
          "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg",
          "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg",
          "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg",
          "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016110/product-image/2409L_61_d.jpg",
          "https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058169/product-image/2409L_61_o.png",
          "https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058159/product-image/2409L_61_p.png"
        ]
      },
      selectedSize: null,
      selectedColor: null,
      selectedImageIndex: 0,
      canAddToCart: false,
    }

    this.setSelectedImageIndex = this.setSelectedImageIndex.bind(this);
  }

  handleSelectSize(size) {
    this.setState({ selectedSize: size }, this.updateAddToCartStatus);
  }

  handleSelectColor(color) {
    this.setState({ selectedColor: color }, this.updateAddToCartStatus);
  }

  handleSelectImage(index) {
    this.setState({ selectedImageIndex: index });
  }

  updateAddToCartStatus() {
    const { selectedSize, selectedColor } = this.state;
    this.setState({ canAddToCart: selectedSize && selectedColor });
  }

  addToCart() {
    throw new Error('Not implemented');
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
              images={productDetails.images}
              selectedImageIndex={selectedImageIndex}
              setSelectedImageIndex={this.setSelectedImageIndex}
            />
            <div className='product-info'>
              <h1>{productDetails.title}</h1>
              <div>
                Attributes here
              </div>
              <div>Price: {this.formatPrice(productDetails.price)}</div>
              <button disabled={!canAddToCart} onClick={this.addToCart}>Add to Cart btn</button>
              <div className='product-description'>description</div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default ProductDetailPage;