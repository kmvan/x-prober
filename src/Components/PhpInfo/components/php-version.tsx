import { observer } from 'mobx-react-lite'
import { FC, useCallback, useEffect } from 'react'
import { CardLink } from '../../Card/components/card-link'
import { serverFetch } from '../../Fetch/server-fetch'
import { gettext } from '../../Language'
import { OK } from '../../Rest/http-status'
import { template } from '../../Utils/components/template'
import { versionCompare } from '../../Utils/components/version-compare'
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
