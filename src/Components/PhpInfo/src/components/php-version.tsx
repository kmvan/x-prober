import React, { useCallback, useEffect } from 'react'
import store from '../stores'
import serverFetch from '@/Fetch/src/server-fetch'
import { OK } from '@/Restful/src/http-status'
import versionCompare from '@/Helper/src/components/version-compare'
import template from '@/Helper/src/components/template'
import { gettext } from '@/Language/src'
import CardLink from '@/Card/src/components/card-link'
import { observer } from 'mobx-react-lite'

const PhpInfoPhpVersion = observer(() => {
  const fetch = useCallback(async () => {
    const { data, status } = await serverFetch('latest-php-version')

    if (status === OK) {
      const { version, date } = data
      store.setLatestPhpVersion(version)
      store.setLatestPhpDate(date)
    }
  }, [])

  useEffect(() => {
    fetch()
  }, [])

  const {
    conf: { version },
    latestPhpVersion,
  } = store
  const compare = versionCompare(version, latestPhpVersion)

  return (
    <CardLink
      href='https://www.php.net/'
      title={gettext('Visit PHP.net Official website')}>
      {version}
      {compare === -1
        ? ' ' +
          template(gettext('(Latest ${latestPhpVersion})'), {
            latestPhpVersion,
          })
        : ''}
    </CardLink>
  )
})

export default PhpInfoPhpVersion
