import { observer } from 'mobx-react-lite'
import React, { FC, useCallback, useEffect } from 'react'
import { CardLink } from '../../../Card/src/components/card-link'
import { serverFetch } from '../../../Fetch/src/server-fetch'
import { gettext } from '../../../Language/src'
import { OK } from '../../../Restful/src/http-status'
import { template } from '../../../Utils/src/components/template'
import { versionCompare } from '../../../Utils/src/components/version-compare'
import { PhpInfoConstants } from '../constants'
import { PhpInfoStore } from '../stores'
export const PhpInfoPhpVersion: FC = observer(() => {
  const {
    conf: { version },
  } = PhpInfoConstants
  const { setLatestPhpVersion, setLatestPhpDate, latestPhpVersion } =
    PhpInfoStore
  const fetch = useCallback(async () => {
    const { data, status } = await serverFetch('latest-php-version')
    if (status === OK) {
      const { version, date } = data
      setLatestPhpVersion(version)
      setLatestPhpDate(date)
    }
  }, [setLatestPhpDate, setLatestPhpVersion])
  useEffect(() => {
    fetch()
  }, [fetch])
  const compare = versionCompare(version, latestPhpVersion)
  return (
    <CardLink
      href='https://www.php.net/'
      title={gettext('Visit PHP.net Official website')}
    >
      {version}
      {compare === -1
        ? ` ${template(gettext('(Latest {{latestPhpVersion}})'), {
            latestPhpVersion,
          })}`
        : ''}
    </CardLink>
  )
})
