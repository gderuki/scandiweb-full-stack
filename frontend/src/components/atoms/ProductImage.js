import React, { Component } from 'react';

class ProductImage extends Component {
    render() {
        const { imageUrl, altText = 'Product Image' } = this.props;
        return <img src={imageUrl} alt={altText} style={{ maxWidth: '100%', height: 'auto' }} />;
    }
}

export default ProductImage;