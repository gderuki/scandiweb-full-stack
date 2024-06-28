// Node Modules
import React from 'react';

// Aliased Imports or Custom Modules
import ProductCard from 'molecules/ProductCard';
import { getPriceInCurrency, getImageUrl } from 'helpers/productHelpers';

// Styles/CSS
import './ProductGrid.css';

class ProductGrid extends React.Component {
  render() {
    const { products } = this.props;

    return (
      <div className='containerStyle'>
        {products.map(product => {
          return (
            <ProductCard
              key={product.id}
              productId={product.id}
              productSlug={product.name.toLowerCase().replace(/ /g, '-')}
              title={product.name}
              price={getPriceInCurrency(product)}
              imageUrl={getImageUrl(product)}
            />
          );
        })}
      </div>
    );
  }
}

export default ProductGrid;