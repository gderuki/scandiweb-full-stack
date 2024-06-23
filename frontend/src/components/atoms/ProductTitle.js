import React, { Component } from 'react';

class ProductTitle extends Component {
    render() {
        const { title } = this.props;
        return <h2>{title}</h2>;
    }
}

export default ProductTitle;