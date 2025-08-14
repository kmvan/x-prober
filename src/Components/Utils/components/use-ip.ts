import { useEffect, useState } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { OK } from '@/Components/Rest/http-status.ts';interface UseIpProps {
  ip: string;
  msg: string;
  isLoading: boolean;
}
export const useIp = (type: 4 | 6): UseIpProps => {
  const [data, setData] = useState<UseIpProps>({
    ip: '',
    msg: gettext('Loading...'),
    isLoading: true,
  });
  useEffect(() => {
    const fetchData = async () => {
      const res = await fetch(`https://ipv${type}.inn-studio.com/ip/?json`);
      await res
        .json()
        .catch(() => {
          setData({ ip: '', msg: gettext('Not support'), isLoading: false });
        })
        .then((ipData) => {
          if (ipData?.ip && res.status === OK) {
            setData({ ip: ipData.ip, msg: '', isLoading: false });
            return;
          }
          setData({
            ip: '',
            msg: gettext('Can not fetch IP'),
            isLoading: false,
          });
        });
    };
    fetchData();
  }, [type]);
  return data;
};
