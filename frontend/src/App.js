import React, { Component } from 'react';
import './App.css';
import StickyNavbar from 'components/organisms/StickyNavbar';
import ProductListing from 'components/pages/ProductListing';
import { Route, Redirect, Switch } from 'react-router-dom';
import { withRouter } from "react-router";
import ProductDetailPage from 'components/pages/ProductDetailPage';
import ROUTE_PATHS from 'constants/RoutePaths';
import NotFoundPage from 'components/pages/NotFoundPage';

class App extends Component {
  state = {
    displayNavbar: true,
  };

  componentDidMount() {
    this.unlisten = this.props.history.listen((location, action) => {
      this.handleRouteChange(location);
    });
    this.handleRouteChange(this.props.location);
  }

  componentDidUpdate(prevProps) {
    if (this.props.location !== prevProps.location) {
      this.handleRouteChange(this.props.location);
    }
  }

  componentWillUnmount() {
    if (this.unlisten) {
      this.unlisten();
    }
  }

  handleRouteChange = (location) => {
    this.setState({ displayNavbar: location.pathname !== ROUTE_PATHS.NOT_FOUND });
  }

  render() {
    return (
      <div>
        {this.state.displayNavbar ? <StickyNavbar /> : null}
        <Switch>
          <Route exact path={ROUTE_PATHS.HOME} render={() => <Redirect to={ROUTE_PATHS.DEFAULT_REDIRECT} />} />
          <Route path={ROUTE_PATHS.CATEGORY} component={ProductListing} />
          <Route path={ROUTE_PATHS.PRODUCT} component={ProductDetailPage} />
          <Route path={ROUTE_PATHS.NOT_FOUND} component={NotFoundPage} />
          <Route render={() => <Redirect to={ROUTE_PATHS.NOT_FOUND} />} />
        </Switch>
      </div>
    );
  }
}

export default withRouter(App);