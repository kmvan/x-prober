import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import restfulFetch from '@/Fetch/src/restful-fetch'
import { OK } from '@/Restful/src/http-status'
import versionCompare from '@/Helper/src/components/version-compare'
import template from '@/Helper/src/components/template'
import { gettext } from '@/Language/src'
import CardLink from '@/Card/src/components/card-link'

@observer
export default class PhpInfoPhpVersion extends Component {
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
            template(gettext('(Latest ${latestPhpVersion})'), {
              latestPhpVersion,
            })
          : ''}
      </CardLink>
    )
  }
}
