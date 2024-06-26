import React, { Component } from 'react';
import ProductGrid from 'organisms/ProductGrid';
import './ProductListing.css';

class ProductListing extends Component {
  constructor(props) {
    super(props);

    const products = [
      { id: 1, category: 'women', title: 'Apple AirTag', price: 19.99, imageUrl: 'https://picsum.photos/331/331' },
      { id: 2, category: 'men', title: 'Sony WI-XB420', price: 229.99, imageUrl: 'https://picsum.photos/331/331' },
      { id: 3, category: 'men', title: '3.5mm jack', price: 3.99, imageUrl: 'https://picsum.photos/331/331' },
      { id: 4, category: 'kids', title: 'Nice', price: 69.99, imageUrl: 'https://picsum.photos/331/331' },
      { id: 5, category: 'men', title: 'Some jacket', price: 343.99, imageUrl: 'https://picsum.photos/331/331' },
      { id: 6, category: 'women', title: 'A bag', price: 129.99, imageUrl: 'https://picsum.photos/331/331' },
      { id: 7, category: 'women', title: 'And a brother', price: 2129.99, imageUrl: 'https://picsum.photos/331/331' },
      { id: 8, category: 'women', title: 'And a sister', price: 425.00, imageUrl: 'https://picsum.photos/331/331' },
    ];

    this.state = {
      products: products, // dummy products
    };
  }

  render() {
    const { categoryName } = this.props.match.params;

    const filteredProducts = this.state.products.filter(product =>
      categoryName ? product.category === categoryName : true
    );

    return (
      <div className="product-listing">
        <h1 className='product-title'>{categoryName || 'Product Listing'}</h1>
        <ProductGrid products={filteredProducts} />
      </div>
    );
  }
}

export default ProductListing;