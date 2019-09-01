import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import restfulFetch from '~components/Fetch/src/restful-fetch'
import { OK } from '~components/Restful/src/http-status'
import versionCompare from '~components/Helper/src/components/version-compare'
import { template } from 'lodash-es'
import { gettext } from '~components/Language/src'
import CardLink from '~components/Card/src/components/card-link'

@observer
class PhpInfoPhpVersion extends Component {
  public componentDidMount() {
    this.fetch()
  }

  private fetch = async () => {
    await restfulFetch('latest-php-version')
      .then(([{ status }, { version, date }]) => {
        if (status === OK) {
          store.setLatestPhpVersion(version)
          store.setLatestPhpDate(date)
        }
      })
      .catch(e => {})
  }
  public render() {
    const {
      conf: { version },
      latestPhpVersion,
    } = store
    const compare = versionCompare(version, latestPhpVersion)

    return (
      <CardLink
        href='https://www.php.net/'
        title={gettext('Visit PHP.net Official website')}
      >
        {version}
        {compare === -1
          ? ' ' +
            template(gettext('(Latest <%= latestPhpVersion %>)'))({
              latestPhpVersion,
            })
          : ''}
      </CardLink>
    )
  }
}

export default PhpInfoPhpVersion
