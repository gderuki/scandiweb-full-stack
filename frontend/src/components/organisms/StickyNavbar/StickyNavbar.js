// Node Modules
import React, { Component } from 'react';
import { withRouter, Link } from 'react-router-dom';

// Custom Modules
import Button from 'atoms/Button';
import CartIcon from 'icons/CartIcon';

import CartOverlay from 'organisms/CartOverlay';
import withCart from 'hoc/withCart';
import { withApolloClient } from 'hoc/withApolloClient';
import { GET_CATEGORIES } from 'graphql/category/getCategories';

// Styles/CSS
import './StickyNavbar.css';

class StickyNavbar extends Component {
  constructor(props) {
    super(props);
    this.state = { categories: [], scrolled: false, highZIndex: false };
  }

  handleScroll = () => {
    const isScrolled = window.scrollY > 0;
    if (isScrolled !== this.state.scrolled) {
      this.setState({ scrolled: isScrolled });
    }
  };

  setHighZIndex = () => {
    this.setState({ highZIndex: true });
  };

  resetZIndex = () => {
    this.setState({ highZIndex: false });
  };

  componentDidMount() {
    const { apolloClient } = this.props;
    apolloClient
      .query({
        query: GET_CATEGORIES,
      })
      .then(result => {
        this.setState({ categories: result.data.categories })
      })
      .catch(error => console.error("Error fetching attributes:", error));

    window.addEventListener('scroll', this.handleScroll);
    document.addEventListener('BIG_IMAGE_OPENED', this.setHighZIndex);
    document.addEventListener('BIG_IMAGE_CLOSED', this.resetZIndex);
  }

  componentWillUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
    document.removeEventListener('BIG_IMAGE_OPENED', this.setHighZIndex);
    document.removeEventListener('BIG_IMAGE_CLOSED', this.resetZIndex);
  }

  toggleCartOverlay = () => {
    this.setState(prevState => ({ isCartOverlayVisible: !prevState.isCartOverlayVisible }));
  };

  render() {
    const { categories, scrolled, highZIndex } = this.state;
    const { selectedCategoryName } = this.props;
    const categoryName = selectedCategoryName ? selectedCategoryName : localStorage.getItem('selectedCategory');
    const navbarStyle = highZIndex ? { zIndex: 0 } : {};
    const { isCartOverlayVisible, toggleCartOverlay, cartItems } = this.props;

    return (
      <nav
        className={`sticky-navbar ${scrolled ? 'scrolled' : ''}`}
        style={navbarStyle}
      >
        <div className="navbar-container">
          <div className="menu-items">
            {categories.map(({ name }) => (
              <Link
                key={name}
                to={`/category/${name}`}
                onClick={() => this.props.selectCategory(name)}
                className={categoryName === name ? 'active' : ''}
                data-testid={categoryName === name ? 'active-category-link' : 'category-link'}
              >
                {name.charAt(0).toUpperCase() + name.slice(1)}
              </Link>
            ))}
          </div>
          <div className="cart-container">
            <Button
              className="cart-button"
              icon={<CartIcon />}
              onClick={toggleCartOverlay}
            />
            {isCartOverlayVisible
              &&
              <CartOverlay
                emptyCart={this.emptyCart}
                addToCart={this.props.addToCart}
                removeFromCart={this.props.removeFromCart}
                clearCart={this.props.clearCart}
                items={cartItems}
                onClose={toggleCartOverlay}
              />
            }
          </div>
        </div>
      </nav>
    );
  }
}

export default
  withApolloClient(
    withCart(
      withRouter(
        StickyNavbar
      )
    )
  );