import { CardLink } from '@/Card/src/components/card-link'
import { serverFetch } from '@/Fetch/src/server-fetch'
import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import { template } from '@/Utils/src/components/template'
import { versionCompare } from '@/Utils/src/components/version-compare'
import { observer } from 'mobx-react-lite'
import React, { useCallback, useEffect } from 'react'
import { PhpInfoStore } from '../stores'
export const PhpInfoPhpVersion = observer(() => {
  const {
    setLatestPhpVersion,
    setLatestPhpDate,
    latestPhpVersion,
    conf: { version },
  } = PhpInfoStore
  const fetch = useCallback(async () => {
    const { data, status } = await serverFetch('latest-php-version')
    if (status === OK) {
      const { version, date } = data
      setLatestPhpVersion(version)
      setLatestPhpDate(date)
    }
  }, [])
  useEffect(() => {
    fetch()
  }, [])
  const compare = versionCompare(version, latestPhpVersion)
  return (
    <CardLink
      href='https://www.php.net/'
      title={gettext('Visit PHP.net Official website')}>
      {version}
      {compare === -1
        ? ' ' +
          template(gettext('(Latest {{latestPhpVersion}})'), {
            latestPhpVersion,
          })
        : ''}
    </CardLink>
  )
})
