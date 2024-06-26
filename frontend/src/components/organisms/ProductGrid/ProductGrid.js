import React from 'react';
import './ProductGrid.css';
import ProductCard from 'components/molecules/ProductCard';

class ProductGrid extends React.Component {
  render() {
    const { products } = this.props;

    return (
      <div className='containerStyle'>
        {products.map(product => (
          <ProductCard
            key={product.id}
            productId={product.id}
            title={product.title}
            price={product.price}
            imageUrl={product.imageUrl}
          />
        ))}
      </div>
    );
  }
}

export default ProductGrid;