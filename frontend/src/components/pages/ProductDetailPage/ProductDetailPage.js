import React, { Component } from 'react';
import './ProductDetailPage.css';
import ImageGallery from 'organisms/ImageGallery';
import AttributeSet from 'molecules/AttributeSet';
import PriceTag from 'molecules/PriceTag';
import Button from 'atoms/Button';

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
        ],
        attributes: [
          {
            id: "Color",
            name: "Color",
            type: "swatch",
            __typename: "AttributeSet",
            items: [
              {
                id: "Color",
                value: "#44FF03",
                displayValue: "Green",
                __typename: "Attribute"
              },
              {
                id: "Color",
                value: "#03FFF7",
                displayValue: "Cyan",
                __typename: "Attribute"
              },
              {
                id: "Color",
                value: "#030BFF",
                displayValue: "Blue",
                __typename: "Attribute"
              },
              {
                id: "Color",
                value: "#000000",
                displayValue: "Black",
                __typename: "Attribute"
              },
              {
                id: "Color",
                value: "#FFFFFF",
                displayValue: "White",
                __typename: "Attribute"
              }
            ]
          },
          {
            id: "Capacity",
            name: "Capacity",
            type: "text",
            __typename: "AttributeSet",
            items: [
              {
                id: "Capacity",
                value: "512G",
                displayValue: "512G",
                __typename: "Attribute"
              },
              {
                id: "Capacity",
                value: "1T",
                displayValue: "1T",
                __typename: "Attribute"
              }
            ]
          }
        ],
        description: "\n<h3>Magic like you’ve never heard</h3>\n<p>AirPods Pro have been designed to deliver Active Noise Cancellation for immersive sound, Transparency mode so you can hear your surroundings, and a customizable fit for all-day comfort. Just like AirPods, AirPods Pro connect magically to your iPhone or Apple Watch. And they’re ready to use right out of the case.\n\n<h3>Active Noise Cancellation</h3>\n<p>Incredibly light noise-cancelling headphones, AirPods Pro block out your environment so you can focus on what you’re listening to. AirPods Pro use two microphones, an outward-facing microphone and an inward-facing microphone, to create superior noise cancellation. By continuously adapting to the geometry of your ear and the fit of the ear tips, Active Noise Cancellation silences the world to keep you fully tuned in to your music, podcasts, and calls.\n\n<h3>Transparency mode</h3>\n<p>Switch to Transparency mode and AirPods Pro let the outside sound in, allowing you to hear and connect to your surroundings. Outward- and inward-facing microphones enable AirPods Pro to undo the sound-isolating effect of the silicone tips so things sound and feel natural, like when you’re talking to people around you.</p>\n\n<h3>All-new design</h3>\n<p>AirPods Pro offer a more customizable fit with three sizes of flexible silicone tips to choose from. With an internal taper, they conform to the shape of your ear, securing your AirPods Pro in place and creating an exceptional seal for superior noise cancellation.</p>\n\n<h3>Amazing audio quality</h3>\n<p>A custom-built high-excursion, low-distortion driver delivers powerful bass. A superefficient high dynamic range amplifier produces pure, incredibly clear sound while also extending battery life. And Adaptive EQ automatically tunes music to suit the shape of your ear for a rich, consistent listening experience.</p>\n\n<h3>Even more magical</h3>\n<p>The Apple-designed H1 chip delivers incredibly low audio latency. A force sensor on the stem makes it easy to control music and calls and switch between Active Noise Cancellation and Transparency mode. Announce Messages with Siri gives you the option to have Siri read your messages through your AirPods. And with Audio Sharing, you and a friend can share the same audio stream on two sets of AirPods — so you can play a game, watch a movie, or listen to a song together.</p>\n"
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

  parseHtmlString(htmlString) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlString, 'text/html');

    const sanitizeContent = (content) => {
      const tempDiv = document.createElement('div');
      tempDiv.textContent = content;
      return tempDiv.innerHTML;
    };

    const createElement = (node) => {
      if (node.nodeType === Node.TEXT_NODE) {
        return node.textContent;
      } else if (node.nodeType === Node.ELEMENT_NODE) {
        let props = { key: Math.random().toString() }; // Adding a unique key for React elements
        if (node.attributes) {
          for (let attr of node.attributes) {
            if (['href', 'src', 'alt', 'title'].includes(attr.name)) {
              props[attr.name] = sanitizeContent(attr.value);
            }
          }
        }
        const children = Array.from(node.childNodes).map(createElement);
        return React.createElement(node.tagName.toLowerCase(), props, ...children);
      }
    };

    return Array.from(doc.body.childNodes).map(createElement);
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
              <h1 className='product-heading'>{productDetails.title}</h1>
              <AttributeSet attributeSets={productDetails.attributes} />
              <PriceTag value={this.formatPrice(productDetails.price)} />
              <Button
                className='add-button'
                label="Add to Cart"
                disabled={!canAddToCart}
                onClick={this.addToCart}
              />
              <div className='product-description'>{this.parseHtmlString(productDetails.description)}</div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default ProductDetailPage;